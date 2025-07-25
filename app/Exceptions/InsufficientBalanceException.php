<?php

namespace App\Exceptions;

use Exception;

class InsufficientBalanceException extends Exception
{
    public function __construct(
        public $message = 'Insufficient balance',
        public $code = 400
    ) {
        parent::__construct($message, $code);
    }
}
