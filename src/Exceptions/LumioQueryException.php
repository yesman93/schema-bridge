<?php

namespace Lumio\Exceptions;

class LumioQueryException extends \Exception {

    /**
     * Exception for query errors
     *
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     *
     * @return LumioQueryException
     */
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null) {
        parent::__construct('Query error: ' . $message, $code, $previous);
    }

}

