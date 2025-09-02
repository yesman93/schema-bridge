<?php

namespace Lumio\Log;

use Exception;
use Throwable;
use Lumio\Auth\Logged;
use Lumio\Container;
use Lumio\Database\DatabaseAdapter;
use Lumio\Traits;

class DatabaseLogger {

    use Traits\Log\LogLevel;

    /**
     * Database adapter - for database interaction
     *
     * @author TB
     * @date 26.5.2025
     *
     * @var DatabaseAdapter
     */
    private DatabaseAdapter $_adapter;

    /**
     * Request ID
     *
     * @author TB
     * @date 26.5.2025
     *
     * @var string
     */
    private static string $_request_id = '';

    /**
     * Database logger
     *
     * @author TB
     *
     * @date 26.5.2025
     * @param Container $container
     *
     * @return void
     * @throws Exception
     */
    public function __construct(Container $container) {
        $this->_adapter = $container->get(DatabaseAdapter::class);
    }

    /**
     * Set the current request ID
     *
     * @author TB
     * @date 26.5.2025
     *
     * @param string $request_id
     *
     * @return void
     */
    public static function request_id(string $request_id): void {
        self::$_request_id = $request_id;
    }

    /**
     * Log given message into database
     *
     * @author TB
     * @date 26.5.2025
     *
     * @param string $level
     * @param string $message
     * @param int|null $model_id
     * @param string|null $model
     * @param array|null $context
     *
     * @return bool
     *
     * @throws Throwable
     */
    private function _insert_log(
        string $level,
        string $message,
        ?int $model_id = null,
        ?string $model = null,
        ?array $context = null
    ): bool {

        if ($message === '') {
            return false;
        }

        $sql = "
            INSERT INTO `log` (`message`, `level`, `environment`, `model`, `model_id`, `context`, `request_id`, `datetime_add`, `user_id_add`)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?)
        ";

        $params = [];
        $params[] = $message;
        $params[] = $level;
        $params[] = LUMIO_ENV;
        $params[] = $model ?? '';
        $params[] = $model_id ?? 0;
        $params[] = $context === null ? null : json_encode($context, JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
        $params[] = self::$_request_id;
        $params[] = Logged::id() ?? 0;

        try {
            return $this->_adapter->query($sql, $params);
        } catch (Throwable $e) {
            Logger::channel('db')->error('Error inserting database log entry. Error: ' . $e->getMessage(), $params);
            return false;
        }
    }

    /**
     * Log emergency into database
     *
     * @author TB
     * @date 26.5.2025
     *
     * @param string $message
     * @param int|null $model_id
     * @param string|null $model
     * @param array|null $context
     *
     * @return bool
     *
     * @throws Throwable
     */
    public function emergency(string $message, ?int $model_id = null, ?string $model = null, ?array $context = null): bool {
        return $this->_insert_log(self::_LEVEL_EMERGENCY, $message, $model_id, $model, $context);
    }

    /**
     * Log alert into database
     *
     * @author TB
     * @date 26.5.2025
     *
     * @param string $message
     * @param int|null $model_id
     * @param string|null $model
     * @param array|null $context
     *
     * @return bool
     *
     * @throws Throwable
     */
    public function alert(string $message, ?int $model_id = null, ?string $model = null, ?array $context = null): bool {
        return $this->_insert_log(self::_LEVEL_ALERT, $message, $model_id, $model, $context);
    }

    /**
     * Log critical into database
     *
     * @author TB
     * @date 26.5.2025
     *
     * @param string $message
     * @param int|null $model_id
     * @param string|null $model
     * @param array|null $context
     *
     * @return bool
     *
     * @throws Throwable
     */
    public function critical(string $message, ?int $model_id = null, ?string $model = null, ?array $context = null): bool {
        return $this->_insert_log(self::_LEVEL_CRITICAL, $message, $model_id, $model, $context);
    }

    /**
     * Log error into database
     *
     * @author TB
     * @date 26.5.2025
     *
     * @param string $message
     * @param int|null $model_id
     * @param string|null $model
     * @param array|null $context
     *
     * @return bool
     *
     * @throws Throwable
     */
    public function error(string $message, ?int $model_id = null, ?string $model = null, ?array $context = null): bool {
        return $this->_insert_log(self::_LEVEL_ERROR, $message, $model_id, $model, $context);
    }

    /**
     * Log warning into database
     *
     * @author TB
     * @date 26.5.2025
     *
     * @param string $message
     * @param int|null $model_id
     * @param string|null $model
     * @param array|null $context
     *
     * @return bool
     *
     * @throws Throwable
     */
    public function warning(string $message, ?int $model_id = null, ?string $model = null, ?array $context = null): bool {
        return $this->_insert_log(self::_LEVEL_WARNING, $message, $model_id, $model, $context);
    }

    /**
     * Log notice into database
     *
     * @author TB
     * @date 26.5.2025
     *
     * @param string $message
     * @param int|null $model_id
     * @param string|null $model
     * @param array|null $context
     *
     * @return bool
     *
     * @throws Throwable
     */
    public function notice(string $message, ?int $model_id = null, ?string $model = null, ?array $context = null): bool {
        return $this->_insert_log(self::_LEVEL_NOTICE, $message, $model_id, $model, $context);
    }

    /**
     * Log info into database
     *
     * @author TB
     * @date 26.5.2025
     *
     * @param string $message
     * @param int|null $model_id
     * @param string|null $model
     * @param array|null $context
     *
     * @return bool
     *
     * @throws Throwable
     */
    public function info(string $message, ?int $model_id = null, ?string $model = null, ?array $context = null): bool {
        return $this->_insert_log(self::_LEVEL_INFO, $message, $model_id, $model, $context);
    }

    /**
     * Log debug into database
     *
     * @author TB
     * @date 26.5.2025
     *
     * @param string $message
     * @param int|null $model_id
     * @param string|null $model
     * @param array|null $context
     *
     * @return bool
     *
     * @throws Throwable
     */
    public function debug(string $message, ?int $model_id = null, ?string $model = null, ?array $context = null): bool {
        return $this->_insert_log(self::_LEVEL_DEBUG, $message, $model_id, $model, $context);
    }

}


