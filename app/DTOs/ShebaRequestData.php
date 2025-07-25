<?php

namespace App\DTOs;

readonly class ShebaRequestData
{
    public function __construct(
        public int $user_id,
        public int $price,
        public string $status,
        public string $fromShebaNumber,
        public string $toShebaNumber,
        public ?string $note = null,
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
