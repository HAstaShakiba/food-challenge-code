<?php

namespace App\Repositories;

use App\Models\Transaction;
use App\DTOs\TransactionData;

interface TransactionRepositoryInterface
{
    public function create(TransactionData $data): Transaction;
} 