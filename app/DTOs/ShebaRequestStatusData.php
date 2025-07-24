<?php

namespace App\DTOs;

class ShebaRequestStatusData
{
    public string $status;
    public ?string $note;

    public function __construct(string $status, ?string $note = null)
    {
        $this->status = $status;
        $this->note = $note;
    }
} 