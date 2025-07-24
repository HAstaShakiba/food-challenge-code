<?php

namespace App\DTOs;

class TransactionData
{
    public function __construct(
        public readonly int $user_id,
        public readonly int $amount,
        public readonly string $type,
        public readonly ?string $note = null,
        public readonly ?int $sheba_request_id = null,
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
