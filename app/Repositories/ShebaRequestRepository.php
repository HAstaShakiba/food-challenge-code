<?php

namespace App\Repositories;

use App\Models\ShebaRequest;
use App\Repositories\ShebaRequestRepositoryInterface;
use App\DTOs\ShebaRequestData;

class ShebaRequestRepository implements ShebaRequestRepositoryInterface
{
    public function create(ShebaRequestData $data): ShebaRequest
    {
        return ShebaRequest::create($data->toArray());
    }

    public function findById(int|string $id): ?ShebaRequest
    {
        return ShebaRequest::find($id);
    }

    public function getPendingOrdered(): \Illuminate\Support\Collection
    {
        return ShebaRequest::where('status', ShebaRequest::STATUS_PENDING)->orderBy('created_at')->get();
    }
} 