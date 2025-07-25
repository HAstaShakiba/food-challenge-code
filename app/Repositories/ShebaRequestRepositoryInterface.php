<?php

namespace App\Repositories;

use App\Models\ShebaRequest;
use App\DTOs\ShebaRequestData;
use Illuminate\Support\Collection;

interface ShebaRequestRepositoryInterface
{
    public function create(ShebaRequestData $data): ShebaRequest;
    public function findById(int|string $id): ?ShebaRequest;
    public function getPendingOrdered(): Collection;
    public function getFiltered(array $filter): Collection;
    public function updateStatus(int|string $id, array $data): ?ShebaRequest;
}
