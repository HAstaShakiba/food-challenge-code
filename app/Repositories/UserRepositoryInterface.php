<?php

namespace App\Repositories;

use App\Models\User;

interface UserRepositoryInterface
{
    public function findById(int|string $id): ?User;
    public function updateBalance(int|string $id, int $amount): bool;
} 