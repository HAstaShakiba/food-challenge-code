<?php

namespace App\DTOs;

class TransactionData
{
    public int $user_id;
    public int $amount;
    public string $type;
    public ?string $note;
    public ?int $sheba_request_id;

    public function __construct(
        int $user_id,
        int $amount,
        string $type,
        ?string $note = null,
        ?int $sheba_request_id = null
    ) {
        $this->user_id = $user_id;
        $this->amount = $amount;
        $this->type = $type;
        $this->note = $note;
        $this->sheba_request_id = $sheba_request_id;
    }

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