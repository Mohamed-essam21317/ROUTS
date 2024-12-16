<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    // The table associated with the model
    protected $table = 'attendance';

    // Define which attributes are mass assignable
    protected $fillable = [
        'student_id',
        'date',
        'check_in_time',
        'check_out_time',
        'face_recognition_verified',
        'verification_timestamp'
    ];

    // Define the relationships
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }
}
