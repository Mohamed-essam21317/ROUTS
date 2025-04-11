<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;


    protected $fillable = [
        'first_name',
        'last_name',
        'role_based_id',
        'name',
        'email',
        'password',
        'role',
        'phone',
    ];

    
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function parentProfile(): HasOne
    {
        return $this->hasOne(ParentProfile::class);
    }

    
    public function fcmToken(): HasOne
    {
        return $this->hasOne(FcmToken::class);
    }

 
    public function routeNotificationForFcm()
    {
        return $this->fcmToken ? $this->fcmToken->fcm_token : null;
    }
}
