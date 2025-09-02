<?php

namespace Lumio\Exceptions;

class LumioViewException extends \Exception {

    /**
     * Exception for view errors
     *
     *
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     *
     * @return LumioViewException
     */
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }

}

