<?php

namespace Lumio;

use Lumio\DTO\IO\FileResponse;
use Lumio\DTO\IO\JsonResponse;
use Lumio\DTO\IO\RedirectResponse;
use Lumio\Factory\ControllerFactory;
use Lumio\Factory\ModelFactory;
use Lumio\IO\Flash;
use Lumio\IO\URIParamsParser;
use Lumio\IO\Request;
use Lumio\IO\Response;
use Lumio\Log\Logger;
use Lumio\Model\BaseModel;
use Lumio\Routing\RouteResolver;
use Lumio\Utilities\Session;
use Lumio\View\View;

class Router {

    /**
     * instance of the container
     *
     *
     * @var Container
     */
    private Container $_container;

    /**
     * Name of the realm
     *
     *
     * @var string
     */
    private string $_realm_name = '';

    /**
     * name of the controller
     *
     *
     * @var string
     */
    private string $_controller_name = '';

    /**
     * name of the action
     *
     *
     * @var string
     */
    private string $_action_name = '';

    /**
     * parameters of the action
     *
     *
     * @var array
     */
    private array $_action_params = [];

    /**
     * Custom URI
     *
     *
     * @var string|null
     */
    private ?string $_uri = null;

    /**
     * Full raw URL
     *
     *
     * @var string|null
     */
    private ?string $_raw_url = null;

    /**
     * routing
     *
     *
     * @param string|null $uri
     *
     * @return \Lumio\Router
     */
    public function __construct(Container $container, ?string $uri = null) {

        $this->_container = $container;

        $this->_uri = $uri;
    }

    /**
     * set the full raw URL
     *
     *
     * @return void
     */
    private function _set_full_url(): void {
        $this->_raw_url = LUMIO_HOST . filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL);
    }

    /**
     * get the full raw URL
     *
     *
     * @return string|null
     */
    public function get_full_url(): ?string {

        if (is_null($this->_raw_url)) {
            $this->_set_full_url();
        }

        return $this->_raw_url;
    }

    /**
     * perform routing
     *
     *
     * @return void
     */
    public function route(): void {

        try {

            $request = $this->_container->get(Request::class);

            // Support for custom routing from config file
            $this->_container->bind(RouteResolver::class, [
                'request' => $request,
                'uri' => $this->_uri,
            ]);
            $resolver = $this->_container->get(RouteResolver::class);

            $this->_realm_name = $resolver->get_realm() ?? '';
            $this->_controller_name = $resolver->get_controller() ?? '';
            $this->_action_name = $resolver->get_action() ?? '';
            $this->_action_params = $resolver->get_params() ?? [];

            URIParamsParser::parse($this->_action_params, $request);

        } catch (\Exception $e) {
            Logger::channel('app')->emergency($e->getMessage());
            $this->_response->status(404)->body($e->getMessage())->send();
        }
    }

    /**
     * get controller instance
     *
     *
     * @return object
     */
    public function get_controller(): object {

        return $this->_container->get(ControllerFactory::class)->make(
            name: $this->_controller_name,
            action: $this->_action_name,
            params: $this->_action_params,
            realm: $this->_realm_name,
        );
    }

    /**
     * Get model instance based on current controller
     *
     *
     * @return BaseModel|null
     *
     * @throws \Exception
     */
    public function get_model(): ?BaseModel {

        try {
            $model = $this->_container->get(ModelFactory::class)->make($this->_controller_name);
        } catch (\Exception $e) {
            $this->_container->get(Response::class)->status(404)->body($e->getMessage())->fail();
        }

        return $model ?? null;
    }

    /**
     * get action
     *
     *
     * @return string
     */
    public function get_action(): string {
        return $this->_action_name;
    }

    /**
     * get action parameters
     *
     *
     * @return array
     */
    public function get_params(): array {
        return $this->_action_params;
    }

    /**
     * get the realm name
     *
     *
     * @return string
     */
    public function get_realm_name() : string {
        return $this->_realm_name;
    }

    /**
     * get the controller name
     *
     *
     * @return string
     */
    public function get_controller_name() : string {
        return $this->_controller_name;
    }

    /**
     * get the controller name in plural
     *
     *
     * @return string
     */
    public function get_controller_name_plural() : string {

        if (str_ends_with($this->_controller_name, 'y')) {
            $plural = substr($this->_controller_name, 0, -1) . 'ies';
        } else {
            $plural = $this->_controller_name . 's';
        }

        return $plural;
    }

}
