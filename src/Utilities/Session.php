<?php

namespace Lumio\Utilities;

class Session {

    /**
     * Start a session
     *
     *
     * @return void
     */
    public static function start(): void {

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    /**
     * Set a session value
     *
     *
     * @param string $key
     * @param mixed $value
     *
     * @return void
     */
    public static function set(string $key, mixed $value): void {
        $_SESSION[$key] = $value;
    }

    /**
     * Get a session value
     *
     *
     * @param string $key
     *
     * @return mixed
     */
    public static function get(string $key): mixed {
        return $_SESSION[$key] ?? false;
    }

    /**
     * Check if given session key exists
     *
     *
     * @param string $key
     *
     * @return bool
     */
    public static function exists(string $key): bool {
        return isset($_SESSION[$key]);
    }

    /**
     * Erase a session key
     *
     *
     * @param string $key
     *
     * @return void
     */
    public static function erase(string $key): void {
        unset($_SESSION[$key]);
    }

    /**
     * Get and erase a session value
     *
     *
     * @param string $key
     *
     * @return mixed
     */
    public static function get_and_forget(string $key): mixed {

        $value = self::get($key);
        self::erase($key);

        return $value;
    }

    /**
     * Destroy the session
     *
     *
     * @return void
     */
    public static function destroy(): void {
        session_destroy();
    }

    /**
     * Add a value to array in session
     *
     *
     * @param string $key
     * @param mixed $value
     * @param bool $unique
     *
     * @return bool
     */
    public static function add_to_array(string $key, mixed $value, bool $unique = true): bool {

        $session_array = self::get($key) ?? [];

        if (is_array($value) && isset($value['key'], $value['value'])) {
            $session_array[$value['key']] = $value['value'];
        } else {

            if ($unique && in_array($value, $session_array, true)) {
                return false;
            }

            $session_array[] = $value;
        }

        self::set($key, $session_array);

        return true;
    }

    /**
     * Remove a value from array in session
     *
     *
     * @param string $key
     * @param mixed $value
     *
     * @return bool
     */
    public static function remove_from_array(string $key, mixed $value): bool {

        if (empty($key)) {
            return false;
        }

        $session_array = self::get($key) ?? [];

        if (is_array($value) && isset($value['key'])) {
            unset($session_array[$value['key']]);
        } else {

            if (($index = array_search($value, $session_array, true)) !== false) {
                unset($session_array[$index]);
            }
        }

        self::set($key, $session_array);

        return true;
    }

    /**
     * Check if value is in session array
     *
     *
     * @param string $key
     * @param mixed $needle
     *
     * @return bool
     */
    public static function is_in_array(string $key, mixed $needle): bool {

        $session_array = self::get($key) ?? [];

        return in_array($needle, $session_array, true);
    }

    /**
     * Regenerate session ID and keep session data
     *
     *
     * @return void
     */
    public static function regenerate(): void {

        if (session_status() !== PHP_SESSION_ACTIVE) {
            self::start();
        }

        $session_data = $_SESSION ?? [];

        session_regenerate_id(true);
        $new_session_id = session_id();

        session_destroy();

        session_id($new_session_id);

        session_start();

        $_SESSION = $session_data;
    }

}
