<?php

namespace Lumio\File;

use Throwable;
use Lumio\Config;

class File {

    /**
     * Lists all files in the given directory and its subdirectories
     *
     *
     * @param string $dir_path
     * @param bool $full_paths
     *
     * @return array List of file paths
     */
    public static function map_directory(string $dir_path, bool $full_paths = true): array {

        $dir_path = rtrim($dir_path, DIRECTORY_SEPARATOR);

        if (!is_dir($dir_path)) {
            return [];
        }

        if (!$full_paths) {

            $all = self::map_directory($dir_path, true);

            return array_map(
                fn($item) => ltrim(str_replace($dir_path, '', $item), '/\\'),
                $all
            );
        }

        $paths = [];

        $entries = @scandir($dir_path);
        if ($entries === false) {
            return [];
        }

        $dirs = [];
        $files = [];
        foreach ($entries as $entry) {

            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $full = $dir_path . DIRECTORY_SEPARATOR . $entry;
            if (is_dir($full)) {
                $dirs[] = $entry;
            } elseif (is_file($full)) {
                $files[] = $entry;
            }
        }

        sort($dirs, SORT_STRING);
        sort($files, SORT_STRING);

        $ordered = array_merge($dirs, $files);

        foreach ($ordered as $entry) {

            $full = $dir_path . DIRECTORY_SEPARATOR . $entry;
            if (is_dir($full)) {

                $paths[] = self::_normalize_path($full);
                $sub = self::map_directory($full, true);
                if (!empty($sub)) {
                    $paths = array_merge($paths, $sub);
                }

            } else {
                $paths[] = self::_normalize_path($full);
            }
        }

        return $paths;
    }

    /**
     * Normalizes a file path by converting backslashes to forward slashes
     * and ensuring Windows drive letters are handled correctly
     *
     *
     * @param string $path
     *
     * @return string
     */
    private static function _normalize_path(string $path): string {

        if (preg_match('#^[A-Z]:\\\\#i', $path, $match)) {

            $prefix = $match[0]; // e.g. C:\
            $rest = substr($path, strlen($prefix));

            return $prefix . str_replace('\\', '/', $rest);
        }

        return str_replace('\\', '/', $path);
    }

    /**
     * Cleans up files in the specified directory based on maximum age in config `app.storage.files_max_age`
     *
     * Deletes files older than the configured maximum age
     *
     *
     * @param string $path
     *
     * @return void
     */
    public static function cleanup(string $path): void {

        if (!is_dir($path)) {
            return;
        }

        $path = rtrim($path, DIRECTORY_SEPARATOR);
        $entries = @scandir($path);
        if ($entries === false) {
            return;
        }

        try {
            $max_age = Config::get('app.storage.files_max_age');
        } catch (Throwable $e) {
            return;
        }

        $time_now = time();
        foreach ($entries as $entry) {

            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $full_path = $path . DIRECTORY_SEPARATOR . $entry;
            if (!file_exists($full_path) || !is_file($full_path)) {
                continue;
            }

            if (filemtime($full_path) < ($time_now - $max_age)) {
                @unlink($full_path);
            }
        }
    }

    /**
     * Splits a file path into directory and filename
     *
     *
     * @param string $path
     *
     * @return array
     */
    public static function split_path(string $path): array {

        $path = self::_normalize_path($path);
        $last_sep = strrpos($path, '/');
        if ($last_sep === false) {
            return ['dir' => '', 'filename' => $path];
        }

        return [
            'dir' => substr($path, 0, $last_sep),
            'filename' => substr($path, $last_sep + 1)
        ];
    }

}
