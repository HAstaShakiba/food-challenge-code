<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreShebaRequest;
use App\Services\ShebaService;
use App\DTOs\ShebaRequestFilterData;
use App\Http\Resources\ShebaRequestResource;
use Illuminate\Http\Request;
use App\DTOs\ShebaRequestStatusData;
use App\Models\ShebaRequest;

class ShebaController extends Controller
{
    protected $shebaService;
    public function __construct(ShebaService $shebaService)
    {
        $this->shebaService = $shebaService;
    }

    /**
     * @OA\Post(
     *     path="/api/sheba",
     *     summary="Create a new Sheba transfer request",
     *     description="Creates a new Sheba transfer request, reserves the amount from the user's balance, and sets the request to pending status.",
     *     tags={"Sheba"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id","price","fromShebaNumber","toShebaNumber"},
     *             @OA\Property(property="user_id", type="integer", example=1, description="User ID"),
     *             @OA\Property(property="price", type="integer", example=500000, description="Transfer amount"),
     *             @OA\Property(property="fromShebaNumber", type="string", example="IR820540102680020817909002", description="Source Sheba number"),
     *             @OA\Property(property="toShebaNumber", type="string", example="IR062960000000100324200001", description="Destination Sheba number"),
     *             @OA\Property(property="note", type="string", example="Test note", description="Optional note")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Request successfully created and set to pending status.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Request is saved successfully and is in pending status"),
     *             @OA\Property(property="request", ref="#/components/schemas/ShebaRequestResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error or insufficient balance."
     *     )
     * )
     */
    public function store(StoreShebaRequest $request)
    {
        $shebaRequest = $this->shebaService->createShebaRequest($request->validated());
        return response()->json([
            'message' => 'Request is saved successfully and is in pending status',
            'request' => new ShebaRequestResource($shebaRequest),
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/sheba",
     *     summary="List Sheba transfer requests (for operator)",
     *     description="Retrieve a list of Sheba transfer requests with optional filtering by status and user.",
     *     tags={"Sheba"},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=false,
     *         description="Filter by status (pending, confirmed, canceled)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         required=false,
     *         description="Filter by user ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of Sheba requests.",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ShebaRequestResource")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $filter = new ShebaRequestFilterData(
            $request->query('status'),
            $request->query('user_id') ? (int)$request->query('user_id') : null
        );
        $requests = $this->shebaService->getFilteredRequests($filter);
        return ShebaRequestResource::collection($requests);
    }

    /**
     * @OA\Post(
     *     path="/api/sheba/{id}",
     *     summary="Confirm or cancel a Sheba request (operator)",
     *     description="Change the status of a Sheba request to confirmed or canceled. If canceled, the amount will be refunded to the user.",
     *     tags={"Sheba"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Sheba request ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="string", enum={"confirmed","canceled"}, example="confirmed", description="New status (confirmed or canceled)"),
     *             @OA\Property(property="note", type="string", example="Canceled by operator", description="Optional note")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Request successfully confirmed or canceled.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Request is Confirmed!"),
     *             @OA\Property(property="request", ref="#/components/schemas/ShebaRequestResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Request is not pending or other error."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Request not found."
     *     )
     * )
     */
    public function update(Request $request, int $id)
    {
        $data = new ShebaRequestStatusData(
            $request->input('status'),
            $request->input('note')
        );
        try {
            $shebaRequest = $this->shebaService->confirmOrCancelRequest($id, $data);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            if ($message === 'Request not found') {
                return response()->json(['message' => $message], 404);
            }
            if ($message === 'Request is not pending') {
                return response()->json(['message' => $message], 400);
            }
            return response()->json(['message' => $message], 500);
        }
        return response()->json([
            'message' => $shebaRequest->status === ShebaRequest::STATUS_CONFIRMED ? 'Request is Confirmed!' : 'Request is Canceled',
            'request' => new ShebaRequestResource($shebaRequest),
        ]);
    }
}
