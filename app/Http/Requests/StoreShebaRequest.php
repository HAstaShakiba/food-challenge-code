<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\Sheba;

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

    public function messages()
    {
        return [
            'fromShebaNumber.regex' => 'شماره شبا باید با IR شروع شده و ۲۴ رقم باشد.',
            'toShebaNumber.regex' => 'شماره شبا باید با IR شروع شده و ۲۴ رقم باشد.',
        ];
    }
} 