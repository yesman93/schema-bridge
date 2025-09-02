<?php

namespace Lumio\Middleware;

use Lumio\Container;
use Lumio\Config;
use Lumio\Contract\MiddlewareContract;
use Lumio\Log\Logger;

class DefaultTimezoneMiddleware implements MiddlewareContract {

    /**
     * Set timezone
     *
     * @author TB
     * @date 15.5.2025
     *
     * @param Container $container
     * @param callable $next
     *
     * @return mixed
     */
    public function handle(Container $container, callable $next): mixed {

        try {
            $timezone = Config::get('app.default_timezone');
        } catch (\Exception $e) {
            $timezone = null;
        }

        if ($timezone !== null && $timezone !== '') {
            date_default_timezone_set($timezone);
        } else {
            date_default_timezone_set('UTC');
            Logger::channel('app')->warning('Default timezone not set in config or empty - using UTC');
        }

        return $next();
    }
}
