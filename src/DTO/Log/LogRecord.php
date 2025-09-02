<?php

namespace Lumio\DTO\Log;

use Lumio\Traits;

class LogRecord {

    use Traits\Log\LogLevel;

    /**
     * datetime
     *
     * @var string
     */
    private string $_datetime;

    /**
     * environment
     *
     * @var string
     */
    private string $_env;

    /**
     * level
     *
     * @var string
     */
    private string $_level;

    /**
     * request ID
     *
     * @var string
     */
    private string $_request_id;

    /**
     * message
     *
     * @var string
     */
    private string $_message;

    /**
     * context
     *
     * @var array|null
     */
    private ?array $_context;

    /**
     * Reader of file logs
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
     * @return string
     */
    public function datetime(): string {
        return $this->_datetime;
    }

    /**
     * Get environment
     *
     * @return string
     */
    public function env(): string {
        return $this->_env;
    }

    /**
     * Get level
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
     * @return string
     */
    public function request_id(): string {
        return $this->_request_id;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function message(): string {
        return $this->_message;
    }

    /**
     * Get context
     *
     * @return array|null
     */
    public function context(): ?array {
        return $this->_context;
    }

}
