<?php

namespace App\Repositories;

use App\Models\Transaction;
use App\DTOs\TransactionData;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function create(TransactionData $data): Transaction
    {
        return Transaction::create($data->toArray());
    }
} 