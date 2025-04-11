<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddressRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id', 'school_id', 'supervisor_id', 'new_address',
        'governorate', 'city', 'landmark', 'status'
    ];

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function school()
    {
        return $this->belongsTo(User::class, 'school_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }
}
