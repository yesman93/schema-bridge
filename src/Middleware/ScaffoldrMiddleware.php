<?php

namespace Lumio\Middleware;

use Exception;
use Lumio\Config;
use Lumio\Container;
use Lumio\Database\Scaffoldr\Scaffoldr;

class ScaffoldrMiddleware {

    /**
     * Handle database synchronization using Scaffoldr (code first approach)
     *
     *
     * @param Container $container
     * @param callable $next
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function handle(Container $container, callable $next): mixed {

        $enabled = Config::get('scaffoldr.enabled');
        if (!$enabled) {
            return $next();
        }

        (new Scaffoldr($container))->sync();

        return $next();
    }
}
