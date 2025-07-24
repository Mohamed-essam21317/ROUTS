<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'transaction_id',
        'amount_cents',
        'success',
        'currency',
        'card_token',
        'raw_data',
        'user_id',
    ];

    protected $casts = [
        'raw_data' => 'array',
        'success' => 'boolean',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
