<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    public function findById(int|string $id): ?User
    {
        return User::find($id);
    }

    public function decrementBalanceWithLock(int $userId, int $amount): bool
    {
        return \DB::transaction(function () use ($userId, $amount) {
            $user = User::where('id', $userId)->lockForUpdate()->first();
            if (!$user || $user->balance < $amount) {
                return false;
            }
            $user->balance -= $amount;
            $user->save();
            return true;
        });
    }

    public function increaseBalanceWithLock(int $userId, int $amount): bool
    {
        return \DB::transaction(function () use ($userId, $amount) {
            $user = User::where('id', $userId)->lockForUpdate()->first();
            if (!$user) {
                return false;
            }
            $user->balance += $amount;
            $user->save();
            return true;
        });
    }
} 