<?php

namespace Lumio\Storage;

use Exception;
use Lumio\Config;

class Path {

    /**
     * Get private storage path
     *
     * This method returns the private storage path, optionally appending a subpath
     *
     * @param string $subpath
     *
     * @return string
     *
     * @throws Exception
     */
    public static function private(string $subpath = ''): string {
        $base = rtrim(Config::get('app.storage.path_private'), '/');
        return $subpath ? $base . '/' . ltrim($subpath, '/') : $base;
    }

    /**
     * Get public storage path
     *
     * This method returns the public storage path, optionally appending a subpath
     *
     * @param string $subpath
     *
     * @return string
     *
     * @throws Exception
     */
    public static function public(string $subpath = ''): string {
        $base = rtrim(Config::get('app.storage.path_public'), '/');
        return $subpath ? $base . '/' . ltrim($subpath, '/') : $base;
    }

    /**
     * Get path for uploaded files
     *
     * @return string
     *
     * @throws Exception
     */
    public static function uploads(): string {
        return self::private('uploads');
    }

    /**
     * Get path for documents
     *
     * @return string
     *
     * @throws Exception
     */
    public static function documents(): string {
        return self::private('documents');
    }

    /**
     * Get path for public images
     *
     * @return string
     *
     * @throws Exception
     */
    public static function public_images(): string {
        return self::public('images');
    }

    // Add more path helpers as needed...

}
