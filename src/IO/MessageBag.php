<?php

namespace Lumio\IO;

class MessageBag {

    /**
     * Errors
     *
     * @var array
     */
    private static array $_errors = [];

    /**
     * Successes
     *
     * @var array
     */
    private static array $_successes = [];

    /**
     * Infos
     *
     * @var array
     */
    private static array $_infos = [];

    /**
     * Warnings
     *
     * @var array
     */
    private static array $_warnings = [];

    /**
     * Add error message
     *
     * @param string $message
     * @param string $name
     *
     * @return void
     */
    public static function error(string $message, string $name = ''): void {

        if (!isset(self::$_errors[$name])) {
            self::$_errors[$name] = [];
        }

        self::$_errors[$name][] = $message;
    }

    /**
     * Add success message
     *
     * @param string $message
     * @param string $name
     *
     * @return void
     */
    public static function success(string $message, string $name = ''): void {

        if (!isset(self::$_successes[$name])) {
            self::$_successes[$name] = [];
        }

        self::$_successes[$name][] = $message;
    }

    /**
     * Add info message
     *
     * @param string $message
     * @param string $name
     *
     * @return void
     */
    public static function info(string $message, string $name = ''): void {

        if (!isset(self::$_infos[$name])) {
            self::$_infos[$name] = [];
        }

        self::$_infos[$name][] = $message;
    }

    /**
     * Add warning message
     *
     * @param string $message
     * @param string $name
     *
     * @return void
     */
    public static function warning(string $message, string $name = ''): void {

        if (!isset(self::$_warnings[$name])) {
            self::$_warnings[$name] = [];
        }

        self::$_warnings[$name][] = $message;
    }

    /**
     * Get error messages
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
     * @param string|null $name
     *
     * @return array
     */
    public static function get_warnings(?string $name = null): array {
        return $name === null ? self::$_warnings : (self::$_warnings[$name] ?? []);
    }

    /**
     * Clear all errors
     *
     * @return void
     */
    public static function clear_errors() : void {
        self::$_errors = [];
    }

    /**
     * Clear all successes
     *
     * @return void
     */
    public static function clear_successes() : void {
        self::$_successes = [];
    }

    /**
     * Clear all infos
     *
     * @return void
     */
    public static function clear_infos() : void {
        self::$_infos = [];
    }

    /**
     * Clear all warnings
     *
     * @return void
     */
    public static function clear_warnings() : void{
        self::$_warnings = [];
    }

    /**
     * Clear all messages
     *
     * @return void
     */
    public static function clear() : void {

        self::clear_errors();
        self::clear_successes();
        self::clear_infos();
        self::clear_warnings();
    }

}
