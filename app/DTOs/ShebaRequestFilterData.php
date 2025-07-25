<?php

namespace App\DTOs;

readonly class ShebaRequestFilterData
{
    public function __construct(
        public ?string $status = null,
        public ?int $user_id = null,
    ) {}
}
