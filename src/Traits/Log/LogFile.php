<?php

namespace Lumio\Traits\Log;

use Lumio\Config;
use Lumio\Traits;

trait LogFile {

    use Traits\File;

    /**
     * Archive log file if it exceeds max configured size
     *
     * @param string $path
     *
     * @return void
     */
    protected static function _archive(string $path): void {

        if (!file_exists($path)) {
            return;
        }

        try {
            $threshold_mb = Config::get('app.logging.size_threshold');
            $logs_root = Config::get('app.logging.path_logs');
        } catch (\Exception $e) {
            $threshold_mb = 5; // default 5 MB
            $logs_root = '';
        }

        if (empty($logs_root) || !is_dir($logs_root)) {
            return;
        }

        $threshold_bytes = $threshold_mb * 1024 * 1024;

        clearstatcache();

        if (filesize($path) >= $threshold_bytes) {

            $timestamp = date('Ymd-His');

            // Extract channel name from file, e.g. request.log => request
            $filename = basename($path);
            $parts = explode('.', $filename);
            $channel = $parts[0] ?? 'unknown';

            // Archive path: logs/archive/<channel>/
            $archive_dir = $logs_root . DIRECTORY_SEPARATOR . 'archive' . DIRECTORY_SEPARATOR . $channel;

            if (!is_dir($archive_dir)) {
                mkdir($archive_dir, 0775, true);
            }

            $archived_path = $archive_dir . DIRECTORY_SEPARATOR . $channel . '_' . $timestamp . '.log';

            rename($path, $archived_path);
            touch($path); // create new empty file
        }
    }

    /**
     * Lock a file for concurrent safe access
     *
     * @param resource $handle
     *
     * @return bool
     */
    protected static function lock_file($handle): bool {
        return flock($handle, LOCK_EX);
    }

    /**
     * Unlock the file
     *
     * @param resource $handle
     *
     * @return void
     */
    protected static function unlock_file($handle): void {
        flock($handle, LOCK_UN);
    }

    /**
     * Retry to acquire lock several times
     *
     * @param string $path
     *
     * @return resource|null
     */
    protected static function get_locked_handle(string $path) {

        try {
            $retries = Config::get('app.logging.lock_retries');
        } catch (\Exception $e) {
            $retries = 5;
        }

        $wait_microseconds = 200000; // 0.2 seconds
        while ($retries-- > 0) {

            $handle = fopen($path, 'a');
            if ($handle && self::lock_file($handle)) {
                return $handle;
            }

            usleep($wait_microseconds);
        }

        return null;
    }

    /**
     * Get all log files for a given channel
     *
     * @param string $channel
     * @param bool $extended
     *
     * @return array
     */
    public static function get_channel_files(string $channel, bool $extended = false): array {

        try {
            $logs_dir = rtrim(Config::get('app.logging.path_logs'), DIRECTORY_SEPARATOR);
            $channels = Config::get('app.logging.channels');
        } catch (\Exception $e) {
            return [];
        }

        if (!isset($channels[$channel])) {
            return [];
        }

        $log_filename = $channels[$channel]; // e.g. 'database.log'
        $log_basename = pathinfo($log_filename, PATHINFO_FILENAME); // e.g. 'database'
        $separator = DIRECTORY_SEPARATOR;
        $files = [];

        clearstatcache();

        // current log if it exists
        $current_path = $logs_dir . $separator . $log_filename;
        if (is_file($current_path)) {

            if ($extended) {

                $mtime = filemtime($current_path);
                $size = filesize($current_path);
                $files[] = [
                    'datetime' => date('j.n.Y G:i:s', $mtime),
                    'size' => $size,
                    'size_readable' => self::readable_size($size),
                    'filename' => $log_filename,
                    'filepath' => $current_path,
                ];

            } else {
                $files[] = $log_filename;
            }
        }

        // archived logs from logs/archive/<basename>/
        $archive_dir = $logs_dir . $separator . 'archive' . $separator . $log_basename;
        if (is_dir($archive_dir)) {

            $archive_files = [];
            foreach (scandir($archive_dir) as $file) {

                if (
                    str_starts_with($file, $log_basename . '_') &&
                    str_ends_with($file, '.log') &&
                    strlen($file) === strlen($log_basename) + 1 + 15 + 4
                ) {
                    $archive_files[] = $file;
                }
            }

            // latest first
            rsort($archive_files);

            foreach ($archive_files as $file) {

                if ($extended) {

                    $file_path = $archive_dir . $separator . $file;
                    $mtime = filemtime($file_path);
                    $size = filesize($file_path);
                    $files[] = [
                        'datetime' => date('j.n.Y G:i:s', $mtime),
                        'size' => $size,
                        'size_readable' => self::readable_size($size),
                        'filename' => $file,
                        'filepath' => $file_path,
                    ];

                } else {
                    $files[] = $file;
                }
            }
        }

        return $files;
    }



}
