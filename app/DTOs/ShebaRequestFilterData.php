<?php

namespace App\DTOs;

class ShebaRequestFilterData
{
    public ?string $status;
    public ?int $user_id;

    public function __construct(?string $status = null, ?int $user_id = null)
    {
        $this->status = $status;
        $this->user_id = $user_id;
    }
} 