<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Parents extends Authenticatable
{
    use HasFactory;

    protected $fillable = ['first_name', 'last_name', 'address', 'email', 'phone', 'gender', 'dob', 'profile_picture', 'fcm_token',];

    // ربط ولي الأمر بالمستخدم الأساسي
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // علاقة Parent بـ Students
    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
