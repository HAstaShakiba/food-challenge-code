<?php

namespace App\DTOs;

readonly class TransactionData
{
    public function __construct(
        public int $user_id,
        public int $amount,
        public string $type,
        public ?string $note = null,
        public ?int $sheba_request_id = null,
    ) {}

    public function toArray(): array
    {
        return [
            'user_id' => $this->user_id,
            'amount' => $this->amount,
            'type' => $this->type,
            'note' => $this->note,
            'sheba_request_id' => $this->sheba_request_id,
        ];
    }
}
