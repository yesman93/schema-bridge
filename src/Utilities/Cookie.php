<?php

namespace Lumio\Utilities;

use Lumio\Config;

class Cookie {

    /**
     * Cookie expiration - 1 hour
     *
     * @author TB
     * @date 15.5.2025
     *
     * @var int
     */
    public const int EXP_1_HOUR     = -1;

    /**
     * Cookie expiration - 1 day
     *
     * @author TB
     * @date 15.5.2025
     *
     * @var int
     */
    public const int EXP_1_DAY      = -2;

    /**
     * Cookie expiration - 1 week
     *
     * @author TB
     * @date 15.5.2025
     *
     * @var int
     */
    public const int EXP_1_WEEK     = -3;

    /**
     * Cookie expiration - 1 month
     *
     * @author TB
     * @date 15.5.2025
     *
     * @var int
     */
    public const int EXP_1_MONTH    = -4;

    /**
     * Cookie expiration - 1 quarter
     *
     * @author TB
     * @date 15.5.2025
     *
     * @var int
     */
    public const int EXP_1_QUARTER  = -5;

    /**
     * Cookie expiration - 1 year
     *
     * @author TB
     * @date 15.5.2025
     *
     * @var int
     */
    public const int EXP_1_YEAR     = -6;

    /**
     * Set a cookie
     *
     * @author TB
     * @date 15.5.2025
     *
     * @param string $name
     * @param mixed $value
     * @param int $expires
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $httponly
     *
     * @return void
     */
    public static function set(
        string $name,
        mixed $value,
        int $expires = 0,
        string $path = '/',
        string $domain = '',
        bool $secure = false,
        bool $httponly = false
    ): void {

        if (self::is_predefined_expiration($expires)) {
            $expires = self::get_expiration($expires);
        }

        if (is_array($value)) {
            $value = json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        setcookie($name, $value, [
            'expires'  => $expires,
            'path'     => $path,
            'domain'   => $domain,
            'secure'   => $secure,
            'httponly' => $httponly,
            'samesite' => 'Lax'
        ]);
    }

    /**
     * Get given cookie value
     *
     * @author TB
     * @date 15.5.2025
     *
     * @param string $name
     *
     * @return mixed
     */
    public static function get(string $name): mixed {

        if (!self::exists($name)) {
            return null;
        }

        $value = $_COOKIE[$name];

        $decoded = json_decode($value, true);

        return json_last_error() === JSON_ERROR_NONE && !empty($decoded) ? $decoded : $value;
    }

    /**
     * Erase given cookie
     *
     * @author TB
     * @date 15.5.2025
     *
     * @param string $name
     * @param string $path
     * @param string $domain
     *
     * @return void
     */
    public static function erase(string $name, string $path = '/', string $domain = ''): void {

        if (!self::exists($name)) {
            return;
        }

        unset($_COOKIE[$name]);
        setcookie($name, '', time() - 3600, $path, $domain);
    }

    /**
     * Erase all cookies with given prefix
     *
     * @author TB
     * @date 15.5.2025
     *
     * @param string $prefix
     *
     * @return void
     */
    public static function erase_prefixed(string $prefix): void {

        foreach ($_COOKIE as $name => $val) {

            if (strpos($name, $prefix) !== false) {
                self::erase($name);
            }
        }
    }

    /**
     * Get given cookie and forget it
     *
     * @author TB
     * @date 15.5.2025
     *
     * @param string $name
     *
     * @return mixed
     */
    public static function get_and_forget(string $name): mixed {

        $value = self::get($name);
        self::erase($name);

        return $value;
    }

    /**
     * Check if cookie exists
     *
     * @author TB
     * @date 15.5.2025
     *
     * @param string $key
     *
     * @return bool
     */
    public static function exists(string $key): bool {
        return isset($_COOKIE[$key]);
    }

    /**
     * Check if given expiration is predefined
     *
     * @author TB
     * @date 15.5.2025
     *
     * @param int $expiration
     *
     * @return bool
     */
    public static function is_predefined_expiration(int $expiration): bool {

        return in_array($expiration, [
            self::EXP_1_HOUR,
            self::EXP_1_DAY,
            self::EXP_1_WEEK,
            self::EXP_1_MONTH,
            self::EXP_1_QUARTER,
            self::EXP_1_YEAR,
        ], true);
    }

    /**
     * Get expiration time by given predefined expiration
     *
     * @author TB
     * @date 15.5.2025
     *
     * @param int $expiration
     *
     * @return int
     */
    public static function get_expiration(int $expiration): int {

        return match ($expiration) {
            self::EXP_1_HOUR     => time() + 3600,
            self::EXP_1_DAY      => time() + 86400,
            self::EXP_1_WEEK     => time() + 604800,
            self::EXP_1_MONTH    => time() + 2592000,
            self::EXP_1_QUARTER  => time() + 7776000,
            self::EXP_1_YEAR     => time() + 31536000,
            default                     => time()
        };
    }
    
}
