<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    public function findById(int|string $id): ?User
    {
        return User::find($id);
    }

    public function updateBalance(int|string $id, int $amount): bool
    {
        $user = User::find($id);
        if (!$user) return false;
        $user->decrement('balance', $amount);
        return true;
    }
} 