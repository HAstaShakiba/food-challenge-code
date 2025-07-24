<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'fromShebaNumber' => 'required|string|regex:/^IR[0-9]{24}$/',
            'toShebaNumber' => 'required|string|regex:/^IR[0-9]{24}$/',
            'note' => 'nullable|string',
            'user_id' => 'required|exists:users,id',
        ];
    }

    public function messages()
    {
        return [
            'fromShebaNumber.regex' => 'شماره شبا باید با IR شروع شده و ۲۴ رقم باشد.',
            'toShebaNumber.regex' => 'شماره شبا باید با IR شروع شده و ۲۴ رقم باشد.',
        ];
    }
} 