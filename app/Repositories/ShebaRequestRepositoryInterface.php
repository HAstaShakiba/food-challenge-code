<?php

namespace App\Repositories;

use App\Models\ShebaRequest;
use App\DTOs\ShebaRequestData;
use App\DTOs\ShebaRequestFilterData;
use App\DTOs\ShebaRequestStatusData;
use Illuminate\Support\Collection;

interface ShebaRequestRepositoryInterface
{
    public function create(ShebaRequestData $data): ShebaRequest;
    public function findById(int|string $id): ?ShebaRequest;
    public function getPendingOrdered(): Collection;
    public function getFiltered(ShebaRequestFilterData $filter): Collection;
    public function updateStatus(int|string $id, ShebaRequestStatusData $data): ?ShebaRequest;
}
