<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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