<?php

namespace Ageras\Sherlock\Exceptions;

use Exception;
use RuntimeException;

class MethodNoImplemented extends RuntimeException
{
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct('Status: ' . $message, $code, $previous);
    }
}
