<?php

namespace App\DTOs;

class ShebaRequestData
{
    public int $user_id;
    public int $price;
    public string $status;
    public string $fromShebaNumber;
    public string $toShebaNumber;
    public ?string $note;

    public function __construct(
        int $user_id,
        int $price,
        string $status,
        string $fromShebaNumber,
        string $toShebaNumber,
        ?string $note = null
    ) {
        $this->user_id = $user_id;
        $this->price = $price;
        $this->status = $status;
        $this->fromShebaNumber = $fromShebaNumber;
        $this->toShebaNumber = $toShebaNumber;
        $this->note = $note;
    }

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