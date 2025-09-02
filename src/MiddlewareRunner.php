<?php

namespace Lumio;

use Lumio\Contract\MiddlewareContract;
use Lumio\Container;

class MiddlewareRunner {

    /**
     * Run given middleware stack
     *
     * @author TB
     * @date 15.5.2025
     *
     * @param MiddlewareContract[] $middleware_stack
     * @param Container $container
     * @param callable $final_handler
     *
     * @return mixed
     */
    public function run(array $middleware_stack, Container $container, callable $final_handler): mixed {

        $next = $final_handler;

        foreach (array_reverse($middleware_stack) as $middleware) {

            $next = function () use ($middleware, $container, $next) {
                return $middleware->handle($container, $next);
            };
        }

        return $next();
    }

}


