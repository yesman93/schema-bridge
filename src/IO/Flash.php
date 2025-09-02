<?php

namespace Lumio\IO;

use Lumio\Config;
use Lumio\Utilities\Session;

class Flash {

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
     *
     * @return void
     */
    public static function error(string $message): void {
        self::$_errors[] = $message;
    }

    /**
     * Add success message
     *
     *
     * @param string $message
     *
     * @return void
     */
    public static function success(string $message): void {
        self::$_successes[] = $message;
    }

    /**
     * Add info message
     *
     *
     * @param string $message
     *
     * @return void
     */
    public static function info(string $message): void {
        self::$_infos[] = $message;
    }

    /**
     * Add warning message
     *
     *
     * @param string $message
     *
     * @return void
     */
    public static function warning(string $message): void {
        self::$_warnings[] = $message;
    }

    /**
     * Get error messages
     *
     *
     * @return array
     */
    public static function get_errors(): array {
        return self::$_errors;
    }

    /**
     * Get success messages
     *
     *
     * @return array
     */
    public static function get_successes(): array {
        return self::$_successes;
    }

    /**
     * Get info messages
     *
     *
     * @return array
     */
    public static function get_infos(): array {
        return self::$_infos;
    }

    /**
     * Get warning messages
     *
     *
     * @return array
     */
    public static function get_warnings(): array {
        return self::$_warnings;
    }

    /**
     * Clear all errors
     *
     *
     * @return void
     */
    public static function clear_errors() : void {
        self::$_errors = [];
    }

    /**
     * Clear all successes
     *
     *
     * @return void
     */
    public static function clear_successes() : void {
        self::$_successes = [];
    }

    /**
     * Clear all infos
     *
     *
     * @return void
     */
    public static function clear_infos() : void {
        self::$_infos = [];
    }

    /**
     * Clear all warnings
     *
     *
     * @return void
     */
    public static function clear_warnings() : void{
        self::$_warnings = [];
    }

    /**
     * Clear all messages
     *
     *
     * @return void
     */
    public static function clear() : void {

        self::clear_errors();
        self::clear_successes();
        self::clear_infos();
        self::clear_warnings();
    }

    /**
     * Absorbs flash messages from session (e.g. after redirect)
     *
     *
     * @return void
     *
     * @throws \Exception
     */
    public static function absorb(): void {

        $name = Config::get('app.view.flash_messages_session');
        $messages = Session::get_and_forget($name);
        if (empty($messages)) {
            return;
        }

        if (!empty($messages['errors'] ?? [])) foreach ($messages['errors'] as $error) {
            self::error($error);
        }

        if (!empty($messages['warnings'] ?? [])) foreach ($messages['warnings'] as $warning) {
            self::warning($warning);
        }

        if (!empty($messages['infos'] ?? [])) foreach ($messages['infos'] as $info) {
            self::info($info);
        }

        if (!empty($messages['successes'] ?? [])) foreach ($messages['successes'] as $success) {
            self::success($success);
        }
    }

}
