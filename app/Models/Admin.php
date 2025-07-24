<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Admin extends Model
{
    use HasFactory, Notifiable;


    protected $fillable = [
        'name',
        'email',
        'school_id',
        'fcm_token',
        'role_based_id',
        'password',
        'user_id',
        'phone'
        // Add other fields as necessary
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
