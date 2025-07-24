<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShebaRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'price',
        'status',
        'fromShebaNumber',
        'toShebaNumber',
        'note',
        'user_id',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CANCELED = 'canceled';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 