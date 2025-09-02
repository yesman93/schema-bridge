<?php

namespace Lumio\Routing;

use Lumio\Config;
use Lumio\IO\URIParamsParser;
use Lumio\IO\Request;

class RouteResolver {

    /**
     * instance of the request object
     *
     * @var Request
     */
    private Request $_request;

    /**
     * array of regex route exceptions
     *
     * @var array
     */
    private array $_exceptions = [];

    /**
     * array of resolved parameters
     *
     * @var array
     */
    private array $_params = [];

    /**
     * Resolver for routes to controller, action and parameters
     *
     * @param Request $request
     *
     * @return RouteResolver
     *
     * @throws \Exception
     */
    public function __construct(Request $request, ?string $uri = null) {

        $this->_request = $request;
        $this->_load_exceptions();
        $this->_resolve($uri);
    }

    /**
     * Load regex exceptions from config
     *
     * @return void
     */
    private function _load_exceptions(): void {

        try {
            $this->_exceptions = Config::get('routes') ?? [];
        } catch (\Throwable $e) {}
    }

    /**
     * Resolve the URI to controller, action and parameters
     *
     * @param string|null $uri
     *
     * @return void
     *
     * @throws \Exception
     */
    private function _resolve(?string $uri = null): void {

        $uri = $this->_sanitize_uri($uri ?? $this->_request->get_request_uri());

        if ($this->_match_exceptions($uri)) {
            return;
        }

        $parts = explode('/', trim($uri, '/'));

        // Support for realms
        $realms = Config::get('app.routing.realms') ?? [];
        if (!empty($parts) && array_key_exists($parts[0] ?? '', $realms)) {
            $this->_params['realm'] = array_shift($parts); // Save and remove the realm part
        } else {
            $this->_params['realm'] = '';
        }

        $controller = $parts[0] ?? null;
        $action = $parts[1] ?? null;
        $params = array_slice($parts, 2);

        if (empty($controller)) {
            $controller = Config::get('app.routing.default_controller');
            $action = Config::get('app.routing.default_action');
        } else {

            if (empty($action)) {
                array_unshift($params, $controller);
                $action = Config::get('app.routing.default_page_action');
                $controller = Config::get('app.routing.default_controller');
            }
        }

        // If there is filter in the URI but no page, page number 1 is injected
        $first_param = $params[0] ?? null;
        if ($first_param !== null && !ctype_digit($first_param) && URIParamsParser::detect_filter($first_param)) {
            $params = array_merge(['1'], $params);
        }

        $this->_params['controller'] = $controller;
        $this->_params['action'] = $action;
        $this->_params['params'] = $params;
    }

    /**
     * Sanitize the URI
     *
     * @param string $uri
     *
     * @return string
     */
    private function _sanitize_uri(string $uri): string {

        $uri = rawurldecode($uri);
        $filtered = preg_replace('/[\x00-\x1F\x7F]/u', '', $uri); // Remove null bytes and control characters

        $filtered = rtrim($filtered, '/');
        $filtered = preg_replace('#/+#', '/', $filtered);

        return $filtered;
    }

    /**
     * Match the URI against regex route exceptions
     *
     * @param string $uri
     *
     * @return bool
     */
    private function _match_exceptions(string $uri): bool {

        if (empty($this->_exceptions)) {
            return false;
        }

        foreach ($this->_exceptions as $pattern => $route) {

            $matches = [];
            if (preg_match($pattern, $uri, $matches)) {

                $this->_params['realm'] = $route['realm'] ?? null;
                $this->_params['controller'] = $route['controller'] ?? null;
                $this->_params['action'] = $route['action'] ?? null;
                $this->_params['params'] = array_slice($matches, 1); // Regex captured groups

                return true;
            }
        }

        return false;
    }

    /**
     * Get the resolved realm name
     *
     * @return string|null
     */
    public function get_realm(): ?string {
        return $this->_params['realm'] ?? null;
    }

    /**
     * Get the resolved controller name
     *
     * @return string|null
     */
    public function get_controller(): ?string {
        return $this->_params['controller'] ?? null;
    }

    /**
     * Get the resolved action name
     *
     * @return string|null
     */
    public function get_action(): ?string {
        return $this->_params['action'] ?? null;
    }

    /**
     * Get the resolved parameters
     *
     * @return array
     */
    public function get_params(): array {
        return $this->_params['params'] ?? [];
    }

}
