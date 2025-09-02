<?php

namespace Lumio\IO;

class MessageBag {

    /**
     * Errors
     *
     * @author TB
     * @date 28.4.2025
     *
     * @var array
     */
    private static array $_errors = [];

    /**
     * Successes
     *
     * @author TB
     * @date 28.4.2025
     *
     * @var array
     */
    private static array $_successes = [];

    /**
     * Infos
     *
     * @author TB
     * @date 28.4.2025
     *
     * @var array
     */
    private static array $_infos = [];

    /**
     * Warnings
     *
     * @author TB
     * @date 28.4.2025
     *
     * @var array
     */
    private static array $_warnings = [];

    /**
     * Add error message
     *
     * @author TB
     * @date 28.4.2025
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
     * @author TB
     * @date 28.4.2025
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
     * @author TB
     * @date 28.4.2025
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
     * @author TB
     * @date 28.4.2025
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
     * @author TB
     * @date 28.4.2025
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
     * @author TB
     * @date 28.4.2025
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
     * @author TB
     * @date 28.4.2025
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
     * @author TB
     * @date 28.4.2025
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
     * @author TB
     * @date 1.5.2025
     *
     * @return void
     */
    public static function clear_errors() : void {
        self::$_errors = [];
    }

    /**
     * Clear all successes
     *
     * @author TB
     * @date 1.5.2025
     *
     * @return void
     */
    public static function clear_successes() : void {
        self::$_successes = [];
    }

    /**
     * Clear all infos
     *
     * @author TB
     * @date 1.5.2025
     *
     * @return void
     */
    public static function clear_infos() : void {
        self::$_infos = [];
    }

    /**
     * Clear all warnings
     *
     * @author TB
     * @date 1.5.2025
     *
     * @return void
     */
    public static function clear_warnings() : void{
        self::$_warnings = [];
    }

    /**
     * Clear all messages
     *
     * @author TB
     * @date 1.5.2025
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
