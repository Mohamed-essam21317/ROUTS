<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'first_name', 'last_name', 'phone_number', 'gender', 'date_of_birth', 'profile_picture'
    ];

    // كل Parent مرتبط بـ User واحد
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

