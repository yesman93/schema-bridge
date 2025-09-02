<?php

namespace Lumio\Contract;

use Lumio\Container;

interface MiddlewareContract {

    /**
     * Handle a request
     *
     * @author TB
     * @date 15.5.2025
     *
     * @param Container $container
     * @param callable $next
     *
     * @return mixed
     */
    public function handle(Container $container, callable $next): mixed;

}
