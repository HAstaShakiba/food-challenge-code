<?php

namespace App\Repositories;

use App\Models\User;

interface UserRepositoryInterface
{
    public function findById(int|string $id): ?User;
    public function decrementBalanceWithLock(int $userId, int $amount): bool;
    public function increaseBalanceWithLock(int $userId, int $amount): bool;
} 