<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="UpdateShebaRequestStatus",
 *     type="object",
 *     title="UpdateShebaRequestStatus",
 *     description="Request body for updating the status of a Sheba transfer request.",
 *     required={"status"},
 *     @OA\Property(property="status", type="string", enum={"confirmed","canceled"}, example="confirmed", description="New status (confirmed or canceled)"),
 *     @OA\Property(property="note", type="string", example="Canceled by operator", description="Optional note")
 * )
 */
class UpdateShebaRequestStatus extends FormRequest
{
    public function rules(): array
    {
        return [
            'status' => 'required|string|in:confirmed,canceled',
            'note' => 'nullable|string',
        ];
    }
} 