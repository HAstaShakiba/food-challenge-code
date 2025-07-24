<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\Sheba;

/**
 * @OA\Schema(
 *     schema="StoreShebaRequest",
 *     type="object",
 *     title="StoreShebaRequest",
 *     description="Request body for creating a Sheba transfer request.",
 *     required={"user_id","price","fromShebaNumber","toShebaNumber"},
 *     @OA\Property(property="user_id", type="integer", example=1, description="User ID"),
 *     @OA\Property(property="price", type="integer", example=500000, description="Transfer amount"),
 *     @OA\Property(property="fromShebaNumber", type="string", example="IR820540102680020817909002", description="Source Sheba number"),
 *     @OA\Property(property="toShebaNumber", type="string", example="IR062960000000100324200001", description="Destination Sheba number"),
 *     @OA\Property(property="note", type="string", example="Test note", description="Optional note")
 * )
 */
class StoreShebaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'price' => 'required|integer|min:1',
            'fromShebaNumber' => ['required', 'string', new Sheba],
            'toShebaNumber' => ['required', 'string', new Sheba],
            'note' => 'nullable|string',
            'user_id' => 'required|exists:users,id',
        ];
    }
} 