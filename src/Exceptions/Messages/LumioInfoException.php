<?php

namespace Lumio\Exceptions\Messages;

class LumioInfoException extends \Exception {

    /**
     * Info message
     *
     *
     * @var string
     */
    private string $_message;

    /**
     * Name that the message is related to
     *
     *
     * @var string
     */
    private string $_name = '';

    /**
     * Exception for info messages
     *
     *
     * @param string $message
     * @param string|null $name
     *
     * @return LumioInfoException
     */
    public function __construct(string $message, ?string $name = null) {

        parent::__construct('Info occured');

        $this->_message = $message;
    }

    /**
     * Get info message
     *
     *
     * @return string
     */
    public function get_message(): string {
        return $this->_message;
    }

    /**
     * Get name that the message is related to
     *
     *
     * @return string
     */
    public function get_name(): string {
        return $this->_name;
    }

}
