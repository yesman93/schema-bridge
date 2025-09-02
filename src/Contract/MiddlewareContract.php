<?php

namespace Lumio\Contract;

use Lumio\Container;

interface MiddlewareContract {

    /**
     * Handle a request
     *
     * @param Container $container
     * @param callable $next
     *
     * @return mixed
     */
    public function handle(Container $container, callable $next): mixed;

}
