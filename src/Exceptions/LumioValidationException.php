<?php

namespace Lumio\Exceptions;

class LumioValidationException extends \Exception {

    /**
     * Exception for validation errors
     *
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     *
     * @return LumioValidationException
     *@author TB     *
     */
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null) {
        parent::__construct('Validation error: ' . $message, $code, $previous);
    }

}

