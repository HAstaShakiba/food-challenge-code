<?php

namespace App\DTOs;

class ShebaRequestData
{
    public function __construct(
        public readonly int $user_id,
        public readonly int $price,
        public readonly string $status,
        public readonly string $fromShebaNumber,
        public readonly string $toShebaNumber,
        public readonly ?string $note = null,
    ) {}

    public function toArray(): array
    {
        return [
            'user_id' => $this->user_id,
            'price' => $this->price,
            'status' => $this->status,
            'fromShebaNumber' => $this->fromShebaNumber,
            'toShebaNumber' => $this->toShebaNumber,
            'note' => $this->note,
        ];
    }
} 