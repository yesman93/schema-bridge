<?php

namespace Lumio\Exceptions\Messages;

class LumioErrorException extends \Exception {

    /**
     * Error message
     *
     * @author TB
     * @date 28.4.2025
     *
     * @var string
     */
    private string $_message;

    /**
     * Name that the message is related to
     *
     * @author TB
     * @date 28.4.2025
     *
     * @var string
     */
    private string $_name = '';

    /**
     * Exception for error messages
     *
     * @author TB
     * @date 28.4.2025
     *
     * @param string $message
     * @param string|null $name
     *
     * @return LumioErrorException
     */
    public function __construct(string $message, ?string $name = null) {

        parent::__construct('Error occured');

        $this->_message = $message;
        $this->_name = $name ?? '';
    }

    /**
     * Get error message
     *
     * @author TB
     * @date 28.4.2025
     *
     * @return string
     */
    public function get_message(): string {
        return $this->_message;
    }

    /**
     * Get name that the message is related to
     *
     * @author TB
     * @date 28.4.2025
     *
     * @return string
     */
    public function get_name(): string {
        return $this->_name;
    }

}
