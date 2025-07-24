<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'type', // debit or credit
        'note',
        'sheba_request_id',
    ];

    public const TYPE_DEBIT = 'debit';
    public const TYPE_CREDIT = 'credit';
    public const NOTE_DEBIT_SHEBA = 'کسر وجه بابت انتقال شبا';
    public const NOTE_CREDIT_SHEBA = 'بازگشت وجه بابت لغو درخواست شبا';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shebaRequest()
    {
        return $this->belongsTo(ShebaRequest::class);
    }
} 