<?php

namespace Lumio\Traits\IO;

trait Message {

    /**
     * Errors
     *
     *
     * @var array
     */
    private static array $_errors = [];

    /**
     * Successes
     *
     *
     * @var array
     */
    private static array $_successes = [];

    /**
     * Infos
     *
     *
     * @var array
     */
    private static array $_infos = [];

    /**
     * Warnings
     *
     *
     * @var array
     */
    private static array $_warnings = [];

    /**
     * Add error message
     *
     *
     * @param string $message
     * @param string $name
     *
     * @return void
     */
    public static function error(string $message, string $name = ''): void {
        self::$_errors[$name][] = $message;
    }

    /**
     * Add success message
     *
     *
     * @param string $message
     * @param string $name
     *
     * @return void
     */
    public static function success(string $message, string $name = ''): void {
        self::$_successes[$name][] = $message;
    }

    /**
     * Add info message
     *
     *
     * @param string $message
     * @param string $name
     *
     * @return void
     */
    public static function info(string $message, string $name = ''): void {
        self::$_infos[$name][] = $message;
    }

    /**
     * Add warning message
     *
     *
     * @param string $message
     * @param string $name
     *
     * @return void
     */
    public static function warning(string $message, string $name = ''): void {
        self::$_warnings[$name][] = $message;
    }

    /**
     * Get error messages
     *
     *
     * @param string|null $name
     *
     * @return array
     */
    public static function get_errors(?string $name = null): array {
        return $name === null ? self::$_errors : (self::$_errors[$name] ?? []);
    }

    /**
     * Get success messages
     *
     *
     * @param string|null $name
     *
     * @return array
     */
    public static function get_successes(?string $name = null): array {
        return $name === null ? self::$_successes : (self::$_successes[$name] ?? []);
    }

    /**
     * Get info messages
     *
     *
     * @param string|null $name
     *
     * @return array
     */
    public static function get_infos(?string $name = null): array {
        return $name === null ? self::$_infos : (self::$_infos[$name] ?? []);
    }

    /**
     * Get warning messages
     *
     *
     * @param string|null $name
     *
     * @return array
     */
    public static function get_warnings(?string $name = null): array {
        return $name === null ? self::$_warnings : (self::$_warnings[$name] ?? []);
    }

}
