<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ShebaRequestResource",
 *     type="object",
 *     title="ShebaRequestResource",
 *     description="Sheba request resource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="price", type="integer", example=500000),
 *     @OA\Property(property="status", type="string", example="pending"),
 *     @OA\Property(property="fromShebaNumber", type="string", example="IR820540102680020817909002"),
 *     @OA\Property(property="toShebaNumber", type="string", example="IR062960000000100324200001"),
 *     @OA\Property(property="note", type="string", example="Test note"),
 *     @OA\Property(property="createdAt", type="string", format="date-time", example="2024-07-24T12:34:56Z")
 * )
 */
class ShebaRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'price' => $this->price,
            'status' => $this->status,
            'fromShebaNumber' => $this->fromShebaNumber,
            'toShebaNumber' => $this->toShebaNumber,
            'note' => $this->note,
            'createdAt' => $this->created_at ? $this->created_at->toIso8601String() : null,
        ];
    }
} 