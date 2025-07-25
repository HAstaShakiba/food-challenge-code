<?php

namespace App\DTOs;

readonly class ShebaRequestStatusData
{
    public function __construct(
        public string $status,
        public ?string $note = null,
    ) {}
}
