<?php

namespace Lumio\DTO\Log;

use Lumio\Traits;

class LogRecord {

    use Traits\Log\LogLevel;

    /**
     * datetime
     *
     * @author TB
     * @date 9.5.2025
     *
     * @var string
     */
    private string $_datetime;

    /**
     * environment
     *
     * @author TB
     * @date 9.5.2025
     *
     * @var string
     */
    private string $_env;

    /**
     * level
     *
     * @author TB
     * @date 9.5.2025
     *
     * @var string
     */
    private string $_level;

    /**
     * request ID
     *
     * @author TB
     * @date 9.5.2025
     *
     * @var string
     */
    private string $_request_id;

    /**
     * message
     *
     * @author TB
     * @date 9.5.2025
     *
     * @var string
     */
    private string $_message;

    /**
     * context
     *
     * @author TB
     * @date 9.5.2025
     *
     * @var array|null
     */
    private ?array $_context;

    /**
     * Reader of file logs
     *
     * @author TB
     * @date 9.5.2025
     *
     * @param string $datetime
     * @param string $env
     * @param string $level
     * @param string $request_id
     * @param string $message
     * @param array|null $context
     *
     * @return void
     */
    public function __construct(string $datetime, string $env, string $level, string $request_id, string $message, ?array $context = null) {

        $this->_datetime = $datetime;
        $this->_env = $env;
        $this->_level = $level;
        $this->_request_id = $request_id;
        $this->_message = $message;
        $this->_context = $context;
    }

    /**
     * Get datetime
     *
     * @author TB
     * @date 9.5.2025
     *
     * @return string
     */
    public function datetime(): string {
        return $this->_datetime;
    }

    /**
     * Get environment
     *
     * @author TB
     * @date 9.5.2025
     *
     * @return string
     */
    public function env(): string {
        return $this->_env;
    }

    /**
     * Get level
     *
     * @author TB
     * @date 9.5.2025
     *
     * @param bool $readable
     *
     * @return string
     */
    public function level(bool $readable = false): string {
        return $readable ? self::get_level_description($this->_level) : $this->_level;
    }

    /**
     * Get request ID
     *
     * @author TB
     * @date 9.5.2025
     *
     * @return string
     */
    public function request_id(): string {
        return $this->_request_id;
    }

    /**
     * Get message
     *
     * @author TB
     * @date 9.5.2025
     *
     * @return string
     */
    public function message(): string {
        return $this->_message;
    }

    /**
     * Get context
     *
     * @author TB
     * @date 9.5.2025
     *
     * @return array|null
     */
    public function context(): ?array {
        return $this->_context;
    }

}
