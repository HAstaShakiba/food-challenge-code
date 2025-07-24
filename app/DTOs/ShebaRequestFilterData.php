<?php

namespace App\DTOs;

class ShebaRequestFilterData
{
    public function __construct(
        public readonly ?string $status = null,
        public readonly ?int $user_id = null,
    ) {}
} 