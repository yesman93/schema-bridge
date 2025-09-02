<?php

namespace Lumio\Exceptions;

class LumioQueryException extends \Exception {

    /**
     * Exception for query errors
     *
     * @author TB
     * @date 27.4.2025
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

