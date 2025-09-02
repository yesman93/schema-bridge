<?php

namespace Lumio\Routing;

use Lumio\Config;
use Lumio\IO\Request;
use Lumio\IO\URIParamsParser;
use Lumio\Log\Logger;
use Lumio\Utilities\Cookie;
use Lumio\Utilities\Http;

class History {

    /**
     * Push an entry into the history stack
     *
     * @author TB
     * @date 15.5.2025
     *
     * @param string $controller
     * @param string $action
     * @param string $title
     * @param Request $request
     * @param string|null $realm
     *
     * @return void
     */
    public static function push(string $controller, string $action, string $title, Request $request, ?string $realm = null): void {

        try {
            $enabled = Config::get('routing.history.enabled');
            $cookie_key = Config::get('routing.history.cookie');
            $max_entries = Config::get('routing.history.max_entries');
            $expiration = Config::get('routing.history.expiration');
        } catch (\Exception $e) {

            Logger::channel('app')
                ->warning('Could not read routing history config while pushing an entry [' . $e->getCode() . ' - ' . $e->getMessage() . ']');

            return;
        }

        if (!$enabled) {
            return;
        }

        $stack = Cookie::get($cookie_key);
        if (empty($stack)) {
            $stack = [];
        }

        // Prevent duplicate route
        if (!empty($stack)) {

            $last = $stack[0];
            $base_uri = URIParamsParser::build_base_uri($controller, $action, $realm);
            if (isset($last['uri']) && has_prefix($last['uri'], $base_uri)) {
                return;
            }
        }

        $entry = [
            'uri' => URIParamsParser::build($controller, $action, $request, $realm),
            'label' => $title,
            'timestamp' => time(),
        ];

        array_unshift($stack, $entry);

        // Limit the stack size
        if (count($stack) > $max_entries) {
            $stack = array_slice($stack, 0, $max_entries);
        }

        // Check the stack size and remove oldest entries if necessary - to fit into cookie max size
        $stack = self::_check_stack_size($stack);

        // Store in cookie
        Cookie::set($cookie_key, $stack, time() + $expiration);
    }

    /**
     * Check the stack size and remove oldest entries if necessary
     *
     * @author TB
     * @date 15.5.2025
     *
     * @param array $stack
     *
     * @return array
     */
    private static function _check_stack_size(array $stack): array {

        // Max size of stack in bytes - leaving space for name, flags etc. up to the 4096 B cookie max size
        $max_size = 3800;

        do {

            $json = json_encode($stack, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $length = strlen($json);

            if ($length <= $max_size) {
                return $stack;
            }

            array_shift($stack); // Remove oldest entry

        } while (!empty($stack));

        return [];
    }

    /**
     * Return full history stack
     *
     * @author TB
     * @date 15.5.2025
     *
     * @return array
     */
    public static function all(): array {

        try {
            $cookie_key = Config::get('routing.history.cookie');
        } catch (\Exception $e) {

            Logger::channel('app')
                ->warning('Could not read routing history config while retrieving entries [' . $e->getCode() . ' - ' . $e->getMessage() . ']');

            return [];
        }

        $stack = Cookie::get($cookie_key);
        if (empty($stack)) {
            return [];
        }

        return $stack;
    }

    /**
     * Return previous location - last entry in the history stack
     *
     * @author TB
     * @date 15.5.2025
     *
     * @return array|null
     */
    public static function previous(): ?array {

        $stack = self::all();
        if (empty($stack)) {
            return null;
        }

        return array_shift($stack);
    }

}
