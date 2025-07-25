<?php

namespace App\Repositories;

use App\Models\ShebaRequest;
use App\DTOs\ShebaRequestData;
use Illuminate\Support\Collection;

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

    public function getPendingOrdered(): Collection
    {
        return ShebaRequest::where('status', ShebaRequest::STATUS_PENDING)->orderBy('created_at')->get();
    }

    public function getFiltered(array $filter): Collection
    {
        $query = ShebaRequest::query();
        if (!empty($filter['status'])) {
            $query->where('status', $filter['status']);
        }
        if (!empty($filter['user_id'])) {
            $query->where('user_id', $filter['user_id']);
        }
        return $query->orderBy('created_at')->get();
    }

    public function updateStatus(int|string $id, array $data): ?ShebaRequest
    {
        $request = ShebaRequest::find($id);
        if (!$request) return null;
        if (isset($data['status'])) {
            $request->status = $data['status'];
        }
        if (array_key_exists('note', $data)) {
            $request->note = $data['note'];
        }
        $request->save();
        return $request;
    }
}
