<?php

namespace Lumio\Factory;

use Lumio\Container;
use Lumio\Router;
use Lumio\IO\Request;
use Lumio\IO\Response;

class RouterFactory {

    /**
     * Make a new router
     *
     *
     * @param Container $container
     * @param string|null $uri
     *
     * @return Router
     */
    public function make(Container $container, ?string $uri = null): Router {

        $router = new Router($container, $uri);
        $router->route();

        return $router;
    }

}
