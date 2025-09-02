<?php

namespace Lumio\Factory;

use Lumio\Config;
use Lumio\Container;
use Lumio\Controller\BaseController;
use Lumio\IO\Request;
use Lumio\IO\Response;

class ControllerFactory {

    /**
     * instance of container
     *
     *
     * @var Container
     */
    private Container $_container;

    /**
     * Factory for creating controllers
     *
     *
     * @param Container $container
     *
     * @return void
     */
    public function __construct(Container $container) {
        $this->_container = $container;
    }

    /**
     * Make a controller
     *
     *
     * @param string $name
     * @param string $action
     * @param array $params
     * @param string|null $realm
     * @param bool $use_implicit_model
     *
     * @return BaseController
     *
     * @throws \Exception
     */
    public function make(
        string $name,
        string $action,
        array $params,
        ?string $realm = null
    ) : BaseController {

        $class = "App\\Controllers\\" . ucfirst($name) . 'Controller';

        // Support for realms
        $is_implicit_model = true;
        if (!empty($realm)) {

            $dir_controllers = Config::get('app.routing.path_controllers');
            $realm_config = Config::get('app.routing.realms.' . $realm) ?? [];
            $realm_name = ucfirst($realm_config['namespace'] ?? '');
            $dir_realm = $dir_controllers . DIRECTORY_SEPARATOR . $realm_name;
            if (!empty($realm_name) && is_dir($dir_realm)) {

                $class = "App\\Controllers\\" . $realm_name . "\\" . ucfirst($name) . 'Controller';

                if (array_key_exists('implicit_model', $realm_config) && empty($realm_config['implicit_model'])) {
                    $is_implicit_model = false;
                }
            }
        }

        if (!class_exists($class)) {
            throw new \Exception('Controller "' . $name . '" not found');
        }

        $model = $is_implicit_model ? $this->_container->get(ModelFactory::class)->make($name) : null;
        $request = $this->_container->get(Request::class);
        $response = $this->_container->get(Response::class);
        $view = $this->_container->get(ViewFactory::class)->make(
            controller: $name,
            action: $action,
            request: $request,
            params: $params,
            realm: $realm
        );

        $this->_container->bind($class, [
            'model' => $model,
            'request' => $request,
            'response' => $response,
            'view' => $view,
        ]);

        return $this->_container->get($class);
    }

}
