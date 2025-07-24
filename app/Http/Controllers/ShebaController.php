<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreShebaRequest;
use App\Services\ShebaService;
use App\DTOs\ShebaRequestFilterData;
use App\Http\Resources\ShebaRequestResource;
use Illuminate\Http\Request;
use App\DTOs\ShebaRequestStatusData;

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

    public function update(Request $request, int $id)
    {
        $data = new ShebaRequestStatusData(
            $request->input('status'),
            $request->input('note')
        );
        $shebaRequest = $this->shebaService->confirmOrCancelRequest($id, $data);
        return response()->json([
            'message' => $shebaRequest->status === \App\Models\ShebaRequest::STATUS_CONFIRMED ? 'Request is Confirmed!' : 'Request is Canceled',
            'request' => new ShebaRequestResource($shebaRequest),
        ]);
    }
} 