<?php

namespace Lumio\File;

use Exception;
use InvalidArgumentException;
use Lumio\Config;
use Lumio\Utilities\Session;

class Storage {

    /**
     * Get public URI for given file
     *
     * This method generates URI that can be used to access given file securely.
     * The URI is generated using a token that is stored in the session.
     *
     *
     * @param string $filepath
     *
     * @return string
     *
     * @throws Exception
     */
    public static function get_public_url(string $filepath): string {
        $uri = Config::get('app.storage.private.uri_show_file');
        return '/' . trim($uri, '/') . '/' . self::generate_token($filepath);
    }

    /**
     * Generate a secure token for the given file path
     *
     * This method generates a token that can be used to access the file securely.
     * The token is stored in the session and has a limited lifetime.
     *
     *
     * @param string $filepath
     *
     * @return string
     *
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public static function generate_token(string $filepath): string {

        if (empty($filepath) || !file_exists($filepath)) {
            throw new InvalidArgumentException('Invalid file path provided');
        }

        $key = Config::get('app.storage.private.session');
        $lifetime = Config::get('app.storage.private.token_lifetime');

        $session = Session::get($key);
        if (empty($session)) {
            $session = [];
        }

        $token = bin2hex(random_bytes(16));

        $session[$token] = [
            'file_path' => $filepath,
            'expires' => time() + $lifetime
        ];

        Session::set($key, $session);

        return $token;
    }

    /**
     * Resolve given token to file path
     *
     * This method retrieves file path associated with the given token.
     * If the token is invalid or expired, it returns null.
     *
     *
     * @param string $token
     *
     * @return string|null
     *
     * @throws Exception
     */
    public static function resolve_token(string $token): ?string {

        $key = Config::get('app.storage.private.session');
        $session = Session::get($key);
        if (empty($session[$token] ?? [])) {
            return null;
        }

        $data = $session[$token];

        if ($data['expires'] < time()) {

            unset($session[$token]);
            Session::set($key, $session);

            return null;
        }

        return $data['file_path'];
    }

}

