<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreShebaRequest;
use App\Services\ShebaService;
use App\DTOs\ShebaRequestFilterData;
use App\Http\Resources\ShebaRequestResource;
use Illuminate\Http\Request;

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

    public function index(Request $request)
    {
        $filter = new ShebaRequestFilterData(
            $request->query('status'),
            $request->query('user_id') ? (int)$request->query('user_id') : null
        );
        $requests = $this->shebaService->getFilteredRequests($filter);
        return ShebaRequestResource::collection($requests);
    }
} 