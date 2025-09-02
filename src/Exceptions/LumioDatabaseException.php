<?php

namespace Lumio\Exceptions;

class LumioDatabaseException extends \Exception {

    /**
     * Exception for validation errors
     *
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     *
     * @return LumioDatabaseException
     */
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }

}

