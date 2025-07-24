<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class EmergencyAlert extends Model
{
    protected $fillable = ['user_id', 'alert_type'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
