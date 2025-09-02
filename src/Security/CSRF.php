<?php

namespace Lumio\Security;

use Exception;
use Lumio\Config;
use Lumio\Container;
use Lumio\Factory\RouterFactory;
use Lumio\IO\Request;
use Lumio\Router;
use Lumio\Routing\RouteResolver;
use Lumio\Utilities\Session;
use Random\RandomException;

class CSRF {

    /**
     * Instance of the container
     *
     *
     * @var Container
     */
    private Container $_container;

    /**
     * If CSRF protection is enabled
     *
     *
     * @var bool
     */
    private bool $_is_enabled;

    /**
     * Current CSRF token
     *
     *
     * @var string
     */
    private string $_token;

    /**
     * Name of the form field, where CSRF token is stored
     *
     *
     * @var string
     */
    private string $_field;

    /**
     * Session key for CSRF tokens
     *
     *
     * @var string
     */
    private string $_session_tokens;

    /**
     * Session key for CSRF token form field names
     *
     *
     * @var string
     */
    private string $_session_names;

    /**
     * Maximum number of CSRF tokens stored in session
     *
     *
     * @var int
     */
    private int $_max_tokens;

    /**
     * Expiration time of CSRF tokens in seconds
     *
     *
     * @var int
     */
    private int $_token_expiration;

    /**
     * Prefix for name of form field, where CSRF token is stored in the form
     *
     *
     * @var string
     */
    private string $_prefix_field;

    /**
     * CSRF manager
     *
     *
     * @param Container $container
     *
     * @return void
     *
     * @throws RandomException
     * @throws Exception
     */
    public function __construct(Container $container) {

        $this->_container = $container;

        $this->_is_enabled = Config::get('security.csrf.enabled');
        if (!$this->_is_enabled) {
            return;
        }

        $this->_session_tokens = Config::get('security.csrf.session_tokens');
        if (!Session::exists($this->_session_tokens)) {
            Session::set($this->_session_tokens, []);
        }

        $this->_session_names = Config::get('security.csrf.session_names');
        if (!Session::exists($this->_session_names)) {
            Session::set($this->_session_names, []);
        }

        $this->_max_tokens = (int)Config::get('security.csrf.max_tokens');

        $this->_token_expiration = (int)Config::get('security.csrf.token_expiration');

        $this->_prefix_field = Config::get('security.csrf.prefix_field');
    }

    /**
     * Check if CSRF protection is enabled
     *
     *
     * @return bool
     */
    public function is_enabled(): bool {
        return $this->_is_enabled;
    }

    /**
     * Generate a new CSRF token and field name and store them in the session and properties
     *
     *
     * @return void
     *
     * @throws RandomException
     */
    public function generate(): void {

        if (!$this->_is_enabled) {
            return;
        }

        $this->_token = bin2hex(random_bytes(32));
        Session::add_to_array($this->_session_tokens, [
            'key' => $this->_token,
            'value' => time() + $this->_token_expiration,
        ]);

        $this->_field = $this->_prefix_field . bin2hex(random_bytes(6));
        Session::add_to_array($this->_session_names, [
            'key' => $this->_token,
            'value' => $this->_field,
        ]);

        $this->_enforce_limit();
    }

    /**
     * Get the current CSRF token
     *
     *
     * @return string
     */
    public function get_token(): string {
        return $this->_token;
    }

    /**
     * Get current name of form field, where CSRF token will be stored
     *
     *
     * @return string
     */
    public function get_field(): string {
        return $this->_field;
    }

    /**
     * Validate given CSRF token
     *
     *
     * @param string $token
     *
     * @return bool
     */
    public function validate(string $token): bool {

        if (!$this->_is_enabled) {
            return true;
        }

        $this->clean_expired();

        $tokens = Session::get($this->_session_tokens);
        $names = Session::get($this->_session_names);
        if (isset($tokens[$token])) {

            unset($tokens[$token]);
            unset($names[$token]);

            return true;
        }

        return false;
    }

    /**
     * Get CSRF token from request (_POST or _SERVER)
     *
     *
     * @return string|null
     *
     * @throws Exception
     */
    public function get_token_from_request(): ?string {

        if (!$this->_is_enabled) {
            return null;
        }

        $request = $this->_container->get(Request::class);
        $names = Session::get($this->_session_names);
        foreach ($names as $token => $name) {

            if ($request->has_post($name)) {
                return $request->post($name);
            }
        }

        return $request->server('HTTP_X_CSRF_TOKEN');
    }

    /**
     * Clean expired CSRF tokens from the session (handles also the form input names)
     *
     *
     * @return void
     */
    private function clean_expired(): void {

        $tokens = Session::get($this->_session_tokens);
        $names = Session::get($this->_session_names);

        $now = time();
        $was_removal = false;
        foreach ($tokens as $token => $expiration) {

            if ($expiration < $now) {

                unset($tokens[$token]);
                unset($names[$token]);

                $was_removal = true;
            }
        }

        if ($was_removal) {
            Session::set($this->_session_tokens, $tokens);
            Session::set($this->_session_names, $names);
        }
    }

    /**
     * Enforce the maximum number of CSRF tokens in the session (handles also the form input names)
     *
     *
     * @return void
     */
    private function _enforce_limit(): void {

        $tokens = Session::get($this->_session_tokens);
        if (count($tokens) > $this->_max_tokens) {

            asort($tokens);

            $names = Session::get($this->_session_names);
            $excess_count = count($tokens) - $this->_max_tokens;
            $removed_tokens = array_slice($tokens, 0, $excess_count, true);
            foreach ($removed_tokens as $token => $_) {

                unset($tokens[$token]);
                unset($names[$token]);
            }

            Session::set($this->_session_tokens, $tokens);
            Session::set($this->_session_names, $names);
        }
    }

    /**
     * Check if the current route is an exception for CSRF protection
     *
     *
     * @return bool
     *
     * @throws Exception
     */
    public function is_exception() {

        try {
            $exceptions = Config::get('security.csrf.exceptions');
        } catch (Exception $e) {
            $exceptions = [];
        }

        if (empty($exceptions)) {
            return true;
        }

        $router = $this->_container->get(Router::class);

        $realm = $router->get_realm_name();
        $controller = $router->get_controller_name();
        $action = $router->get_action();

        $request = $this->_container->get(Request::class);

        foreach ($exceptions as $exception) {

            $e_resolver = new RouteResolver($request, $exception);

            $e_realm = $e_resolver->get_realm();
            $e_controller = $e_resolver->get_controller();
            $e_action = $e_resolver->get_action();

            if ($realm === $e_realm && $controller === $e_controller && $action === $e_action) {
                return true;
            }
        }

        return false;
    }

}
