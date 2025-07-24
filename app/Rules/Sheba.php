<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Sheba implements Rule
{
    public function passes($attribute, $value)
    {
ز        if (!preg_match('/^IR[0-9]{24}$/', $value)) {
            return false;
        }
        $iban = substr($value, 4) . substr($value, 0, 4);
        $iban = str_replace(
            range('A', 'Z'),
            range(10, 35),
            $iban
        );
        return bcmod($iban, 97) == 1;
    }

    public function message()
    {
        return 'شماره شبا نامعتبر است.';
    }
} 