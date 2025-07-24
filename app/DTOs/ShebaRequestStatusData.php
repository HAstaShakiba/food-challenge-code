<?php

namespace App\DTOs;

class ShebaRequestStatusData
{
    public function __construct(
        public readonly string $status,
        public readonly ?string $note = null,
    ) {}
} 