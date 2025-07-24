<?php

namespace App\Repositories;

use App\Models\ShebaRequest;
use App\DTOs\ShebaRequestData;
use App\DTOs\ShebaRequestFilterData;

interface ShebaRequestRepositoryInterface
{
    public function create(ShebaRequestData $data): ShebaRequest;
    public function findById(int|string $id): ?ShebaRequest;
    public function getPendingOrdered(): \Illuminate\Support\Collection;
    public function getFiltered(ShebaRequestFilterData $filter): \Illuminate\Support\Collection;
} 