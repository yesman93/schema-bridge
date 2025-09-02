<?php

namespace Lumio\Log;

use Lumio\Config;
use Lumio\Container;
use Lumio\Traits;

class Logger {

    use Traits\Log\LogLevel;
    use Traits\Log\LogFile;

    /**
     * Current channel for logging
     *
     *
     * @var string|null
     */
    private static ?string $_channel = null;

    /**
     * Request ID
     *
     *
     * @var string
     */
    private static string $_request_id = '';

    /**
     * Set the current request ID
     *
     *
     * @param string $request_id
     *
     * @return void
     */
    public static function request_id(string $request_id): void {
        self::$_request_id = $request_id;
    }

    /**
     * Create a new instance of the logger with the given channel
     *
     *
     * @param string $channel
     *
     * @return self
     */
    public static function channel(string $channel): self {

        self::$_channel = $channel;

        return new self();
    }

    /**
     * Log emergency
     *
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     *
     * @throws \Exception
     */
    public function emergency(string $message, array $context = []): void {
        $this->_write_log(self::_LEVEL_EMERGENCY, $message, $context);
    }

    /**
     * Log alert
     *
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     *
     * @throws \Exception
     */
    public function alert(string $message, array $context = []): void {
        $this->_write_log(self::_LEVEL_ALERT, $message, $context);
    }

    /**
     * Log critical
     *
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     *
     * @throws \Exception
     */
    public function critical(string $message, array $context = []): void {
        $this->_write_log(self::_LEVEL_CRITICAL, $message, $context);
    }

    /**
     * Log error
     *
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     *
     * @throws \Exception
     */
    public function error(string $message, array $context = []): void {
        $this->_write_log(self::_LEVEL_ERROR, $message, $context);
    }

    /**
     * Log warning
     *
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     *
     * @throws \Exception
     */
    public function warning(string $message, array $context = []): void {
        $this->_write_log(self::_LEVEL_WARNING, $message, $context);
    }

    /**
     * Log notice
     *
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     *
     * @throws \Exception
     */
    public function notice(string $message, array $context = []): void {
        $this->_write_log(self::_LEVEL_NOTICE, $message, $context);
    }

    /**
     * Log info
     *
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     *
     * @throws \Exception
     */
    public function info(string $message, array $context = []): void {
        $this->_write_log(self::_LEVEL_INFO, $message, $context);
    }

    /**
     * Log debug
     *
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     *
     * @throws \Exception
     */
    public function debug(string $message, array $context = []): void {
        $this->_write_log(self::_LEVEL_DEBUG, $message, $context);
    }

    /**
     * Write log entry to the log file
     *
     *
     * @param string $level
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    private function _write_log(string $level, string $message, array $context = []): void {

        $level = strtolower($level);
        $channel = self::$_channel ?? 'log';

        try {
            $channels_config = Config::get('app.logging.channels');
            $logs_dir = Config::get('app.logging.path_logs');
        } catch (\Exception $e) {
            return;
        }

        if (!is_nempty_array($channels_config) || empty($logs_dir)) {
            return;
        }

        $channel_path = $channels_config[$channel] ?? 'log.log';

        $file_path = rtrim($logs_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $channel_path;
        $datetime = date('Y-m-d H:i:s');

        try {
            $env = Config::get('init.env');
        } catch (\Exception $e) {
            $env = LUMIO_DEV;
        }

        $entry = implode("\t\t", [
            $datetime,
            $env . '.' . strtoupper($level),
            self::$_request_id,
            $message,
            empty($context) ? '' : json_encode($context)
        ]);

        self::_archive($file_path);

        $fp = fopen($file_path, 'a');
        self::lock_file($fp);
        fwrite($fp, $entry . PHP_EOL);
        self::unlock_file($fp);
    }

}
