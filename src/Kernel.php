<?php

namespace Lumio;

use Lumio\Container;
use Lumio\Database\DatabaseAdapter;
use Lumio\Database\MysqlAdapter;
use Lumio\DTO\Database\DatabaseCredentials;
use Lumio\DTO\IO\FileResponse;
use Lumio\DTO\IO\JsonResponse;
use Lumio\DTO\IO\RedirectResponse;
use Lumio\Exceptions\LumioDatabaseException;
use Lumio\Factory\ControllerFactory;
use Lumio\Factory\ModelFactory;
use Lumio\Factory\RequestFactory;
use Lumio\Factory\RouterFactory;
use Lumio\Factory\ViewFactory;
use Lumio\IO\ResponseManager;
use Lumio\Log\DatabaseLogger;
use Lumio\Log\Logger;
use Lumio\Middleware\CSRFMiddleware;
use Lumio\Middleware\DefaultTimezoneMiddleware;
use Lumio\Middleware\ScaffoldrMiddleware;
use Lumio\Middleware\SessionStartMiddleware;
use Lumio\Middleware\SignedUrlMiddleware;
use Lumio\Router;
use Lumio\IO\Request;
use Lumio\IO\Response;
use Lumio\Routing\RouteResolver;

class Kernel {

    /**
     * container for services
     *
     *
     * @var Container
     */
    private Container $_container;

    /**
     * run of the application
     *
     *
     * @return \Lumio\Kernel
     */
    public function __construct() {

        $this->_container = Container::setup();

        $request = $this->_container->get(Request::class);
        Logger::request_id($request->get_id());
        DatabaseLogger::request_id($request->get_id());
    }

    /**
     * run the application
     *
     *
     * @return void
     *
     * @throws \Exception
     */
    public function boot(): void {

        $router = $this->_container->get(Router::class);

        $controller = $router->get_controller();
        $action = $router->get_action();
        $params = $router->get_params();

        // Set up the controller
        $controller->set_name($router->get_controller_name());
        $controller->set_name_plural($router->get_controller_name_plural());
        $controller->set_realm($router->get_realm_name());

        // Make the container accessible from inside the controller
        $controller->set_container($this->_container);

        // First ever hook of the controller
        $controller->ignite();

        // Middleware stack to run (FIFO) before controller logic
        $middleware_stack = [
            new DefaultTimezoneMiddleware(), // set default timezone
            new SessionStartMiddleware(), // start session
            new SignedUrlMiddleware(), // validate signed URL, if is enabled and signed URL is present
            new CSRFMiddleware(), // validate CSRF token, if is enabled and is not an exception
            new ScaffoldrMiddleware(),
            // add next middlewares here ...
        ];

        $result = (new MiddlewareRunner())->run(
            $middleware_stack,
            $this->_container->get(Container::class),
            function () use ($controller, $action, $params) {

                // Process the action with parameters
                return $controller->process($action, $params);
            }
        );

        // Check if the action result is a typed response - if so, send it
        $response_manager = $this->_container->get(ResponseManager::class);
        $response_manager->respond($result);

        // Standard page rendering
        if ($controller->is_render()) {
            $controller->render();
        }
    }

}
