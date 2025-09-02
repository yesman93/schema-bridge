<?php

namespace Lumio\Middleware;

use Lumio\Contract\MiddlewareContract;
use Lumio\Container;
use Lumio\Utilities\Session;

class SessionStartMiddleware implements MiddlewareContract {

    /**
     * Handle the session startup
     *
     * @param Container $container
     * @param callable $next
     *
     * @return mixed
     */
    public function handle(Container $container, callable $next): mixed {

        if (session_status() !== PHP_SESSION_ACTIVE) {
            Session::start();
        }

        return $next();
    }
}
