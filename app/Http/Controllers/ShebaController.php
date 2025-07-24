<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreShebaRequest;
use App\Services\ShebaService;
use App\Http\Resources\ShebaRequestResource;

class ShebaController extends Controller
{
    protected $shebaService;
    public function __construct(ShebaService $shebaService)
    {
        $this->shebaService = $shebaService;
    }

    public function store(StoreShebaRequest $request)
    {
        $shebaRequest = $this->shebaService->createShebaRequest($request->validated());
        return response()->json([
            'message' => 'Request is saved successfully and is in pending status',
            'request' => new ShebaRequestResource($shebaRequest),
        ], 201);
    }
} 