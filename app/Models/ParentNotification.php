<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'notify_on_approach',
        'fcm_token',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
