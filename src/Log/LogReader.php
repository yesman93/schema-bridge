<?php

namespace Lumio\Log;

use Lumio\Config;
use Lumio\Traits;
use Lumio\DTO\Log\LogRecord;

class LogReader {

    use Traits\Log\LogLevel;
    use Traits\Log\LogFile;

    /**
     * Read log records from given channel
     *
     *
     * @param string $channel
     * @param string|null $file
     *
     * @return LogRecord[]
     */
    public static function read(string $channel, ?string $file = null): array {

        try {

            $path_logs = Config::get('app.logging.path_logs');
            $channel_path = Config::get('app.logging.channels.' . $channel);
            if (empty($channel_path)) {
                $channel_path = 'log.log';
            }
            $session_key = Config::get('app.logging.reader_cache_name');

        } catch (\Throwable $e) {
            return [];
        }

        if (empty($path_logs) || empty($session_key)) {
            return [];
        }

        if (!empty($file)) {

            $log_file = '';
            $files = self::get_channel_files($channel, true);
            if (!empty($files)) foreach ($files as $f) {

                $file_name = $f['filename'] ?? '';
                if ($file != $file_name) {
                    continue;
                }

                $file_path = $f['filepath'] ?? '';
                if (!empty($file_path) && is_file($file_path)) {
                    $log_file = $file_path;
                    break;
                }
            }

        } else {
            $path_logs = rtrim($path_logs, DIRECTORY_SEPARATOR);
            $log_file = $path_logs . DIRECTORY_SEPARATOR . $channel_path;
        }


        if (!file_exists($log_file)) {
            return [];
        }

        clearstatcache();

        $mtime = filemtime($log_file);

        // If data in cache are up to date, return them
        if (isset($_SESSION[$session_key][$channel])) {

            $cached = $_SESSION[$session_key][$channel];
            if (isset($cached['mtime']) && $cached['mtime'] === $mtime) {
                return $cached['data'];
            }
        }

        // Parse and cache the parsed data
        $data = self::_parse_log_file($log_file);
        $_SESSION[$session_key][$channel] = [
            'mtime' => $mtime,
            'data' => $data
        ];

        return $data;
    }

    /**
     * Parses a log file and returns LogRecord DTOs in an array
     *
     *
     * @param string $file_path
     *
     * @return LogRecord[]
     */
    private static function _parse_log_file(string $file_path): array {

        $lines = file($file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return [];
        }

        $records = [];
        $buffer = '';
        foreach ($lines as $line) {

            // each record starts with datetime in format YYYY-MM-DD HH:MM:SS
            $prefix = substr($line, 0, 19);
            $is_new_entry = strlen($prefix) === 19 && $prefix[4] === '-' && $prefix[7] === '-' && $prefix[10] === ' ' && $prefix[13] === ':' && $prefix[16] === ':';
            if ($is_new_entry) {

                if (!empty($buffer)) {

                    $record = self::_parse_log_entry($buffer);
                    if ($record !== null) {
                        $records[] = $record;
                    }

                    $buffer = '';
                }

                $buffer .= $line;

            } else {
                $buffer .= "\n" . $line;
            }
        }

        if (!empty($buffer)) {

            $record = self::_parse_log_entry($buffer);
            if ($record !== null) {
                $records[] = $record;
            }
        }

        return array_reverse($records);
    }

    /**
     * Parses a single log entry and returns a LogRecord DTO or null if parsing fails
     *
     *
     * @param string $line
     *
     * @return LogRecord|null
     */
    private static function _parse_log_entry(string $line): ?LogRecord {

        $parts = explode("\t\t", $line);
        if (count($parts) < 4) {
            return null; // skip malformed lines
        }

        [$datetime, $envlevel, $request_id, $message] = $parts;
        $json = $parts[4] ?? null;

        if (!empty($envlevel) && strpos($envlevel, '.') !== false) {
            [$env, $level] = explode('.', $envlevel);
        } else {
            $env = 'unknown';
            $level = self::_LEVEL_INFO;
        }

        $context = null;
        if (!empty($json)) {
            $decoded = json_decode($json, true);
            $context = is_array($decoded) ? $decoded : null;
        }

        return new LogRecord($datetime, $env, $level, $request_id, $message, $context);
    }

}
