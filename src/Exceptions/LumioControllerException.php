<?php

namespace Lumio\Exceptions;

class LumioControllerException extends \Exception {

    /**
     * Exception for controller errors
     *
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     *
     * @return LumioControllerException
     */
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }

}

