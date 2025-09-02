<?php

namespace Lumio\Exceptions\Messages;

class LumioWarningException extends \Exception {

    /**
     * Warning message
     *
     * @var string
     */
    private string $_message;

    /**
     * Name that the message is related to
     *
     * @var string
     */
    private string $_name = '';

    /**
     * Exception for warning messages
     *
     * @param string $message
     * @param string|null $name
     *
     * @return LumioWarningException
     */
    public function __construct(string $message, ?string $name = null) {

        parent::__construct('Warning occured');

        $this->_message = $message;
        $this->_name = $name ?? '';
    }

    /**
     * Get warning message
     *
     * @return string
     */
    public function get_message(): string {
        return $this->_message;
    }

    /**
     * Get name that the message is related to
     *
     * @return string
     */
    public function get_name(): string {
        return $this->_name;
    }

}
