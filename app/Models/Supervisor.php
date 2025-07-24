<?php

// app/Models/Supervisor.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supervisor extends Model
{
    // Allow mass assignment for the following attributes
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'password',
        'school_id',
        'phone'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
