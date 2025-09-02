<?php

namespace Lumio\Log;

use Lumio\Container;
use Throwable;

class DatabaseLoggerProxy {

    /**
     * Database logger instance
     *
     * @author TB
     * @date 26.5.2025
     *
     * @var Container
     */
    private Container $_container;

    /**
     * Default model for logging
     *
     * @author TB
     * @date 26.5.2025
     *
     * @var string|null
     */
    private ?string $_model = null;

    /**
     * Default model ID for logging
     *
     * @author TB
     * @date 26.5.2025
     *
     * @var int|null
     */
    private ?int $_model_id = null;

    /**
     * Proxy for database logger
     *
     * This class acts as a proxy for the DatabaseLogger, allowing to log messages with values defaults
     *
     * @author TB
     * @date 26.5.2025
     *
     * @param Container $container
     * @param string|null $default_model
     * @param int|null $default_model_id
     *
     * @return void
     */
    public function __construct(Container $container, ?string $default_model = null, ?int $default_model_id = null) {

        $this->_container = $container;
        $this->_model = $default_model;
        $this->_model_id = $default_model_id;
    }

    /**
     * Set default model
     *
     * @author TB
     * @date 26.5.2025
     *
     * @param string $model
     *
     * @return self
     */
    public function model(string $model): self {
        $this->_model = $model;
        return $this;
    }

    /**
     * Set default model ID
     *
     * @author TB
     * @date 26.5.2025
     *
     * @param int $model_id
     *
     * @return self
     */
    public function model_id(int $model_id): self {
        $this->_model_id = $model_id;
        return $this;
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
        $logger = $this->_container->get(DatabaseLogger::class);
        return $logger->emergency($message, $model_id ?? $this->_model_id, $model ?? $this->_model, $context);
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
        $logger = $this->_container->get(DatabaseLogger::class);
        return $logger->alert($message, $model_id ?? $this->_model_id, $model ?? $this->_model, $context);
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
        $logger = $this->_container->get(DatabaseLogger::class);
        return $logger->critical($message, $model_id ?? $this->_model_id, $model ?? $this->_model, $context);
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
        $logger = $this->_container->get(DatabaseLogger::class);
        return $logger->error($message, $model_id ?? $this->_model_id, $model ?? $this->_model, $context);
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
        $logger = $this->_container->get(DatabaseLogger::class);
        return $logger->warning($message, $model_id ?? $this->_model_id, $model ?? $this->_model, $context);
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
        $logger = $this->_container->get(DatabaseLogger::class);
        return $logger->notice($message, $model_id ?? $this->_model_id, $model ?? $this->_model, $context);
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
        $logger = $this->_container->get(DatabaseLogger::class);
        return $logger->info($message, $model_id ?? $this->_model_id, $model ?? $this->_model, $context);
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
        $logger = $this->_container->get(DatabaseLogger::class);
        return $logger->debug($message, $model_id ?? $this->_model_id, $model ?? $this->_model, $context);
    }

}
