<?php

namespace App\Exceptions;

use Exception;

class InsufficientBalanceException extends Exception
{
    public function __construct($message = 'Insufficient balance', $code = 400)
    {
        parent::__construct($message, $code);
    }
} 