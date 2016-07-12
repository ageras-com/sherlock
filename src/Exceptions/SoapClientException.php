<?php

namespace Ageras\Sherlock\Exceptions;

use Exception;
use RuntimeException;

class SoapClientException extends RuntimeException
{
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct('Error: ' . $message, $code, $previous);
    }
}
