<?php

namespace App\Exceptions;

use Exception;

class InsufficientBalanceException extends Exception
{
    public function __construct(
        public readonly string $message = 'Insufficient balance',
        public readonly int $code = 400
    ) {
        parent::__construct($message, $code);
    }
} 