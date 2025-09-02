<?php

namespace Lumio\Auth;

use Lumio\DTO\Auth\LoggedUser;
use Lumio\Utilities\Session;


class Logged {

    /**
     * Session key for storing the logged user
     *
     * @author TB
     * @date 27.4.2025
     *
     * @var string
     */
    private const _SESSION_KEY = 'user';

    /**
     * Currently logged user
     *
     * @author TB
     * @date 27.4.2025
     *
     * @var LoggedUser|null
     */
    private static ?LoggedUser $_user = null;

    /**
     * Login the user
     *
     * @author TB
     * @date 27.4.2025
     *
     * @param LoggedUser $user
     *
     * @return void
     */
    public static function login(LoggedUser $user): void {

        self::$_user = $user;

        Session::set(self::_SESSION_KEY, serialize($user));
    }

    /**
     * Check if a user is logged in
     *
     * @author TB
     * @date 27.4.2025
     *
     * @return bool
     */
    public static function is_logged(): bool {

        self::_load_from_session();

        return !empty(self::$_user) && !empty(self::$_user->get('id'));
    }

    /**
     * Get logged user ID
     *
     * @author TB
     * @date 27.4.2025
     *
     * @return int|null
     */
    public static function id(): ?int {

        self::_load_from_session();

        return !empty(self::$_user) ? self::$_user->get('id') : null;
    }

    /**
     * Get logged user DTO
     *
     * @author TB
     * @date 27.4.2025
     *
     * @return LoggedUser|null
     */
    public static function user(): ?LoggedUser {

        self::_load_from_session();

        return self::$_user;
    }

    /**
     * Logout the user
     *
     * @author TB
     * @date 27.4.2025
     *
     * @return void
     */
    public static function logout(): void {

        self::$_user = null;

        Session::erase(self::_SESSION_KEY);
    }

    /**
     * Update the logged user
     *
     * @author TB
     * @date 27.4.2025
     *
     * @param LoggedUser $user
     *
     * @return void
     */
    public static function update(LoggedUser $user): void {

        self::$_user = $user;

        Session::set(self::_SESSION_KEY, serialize($user));
    }

    /**
     * Load logged user from session if not already loaded
     *
     * @author TB
     * @date 27.4.2025
     *
     * @return void
     */
    private static function _load_from_session(): void {

        if (!is_null(self::$_user)) {
            return;
        }

        $user_data = Session::get(self::_SESSION_KEY);

        if (!empty($user_data)) {

            $unserialized = unserialize($user_data);
            if ($unserialized instanceof LoggedUser) {
                self::$_user = $unserialized;
            }
        }
    }
}
