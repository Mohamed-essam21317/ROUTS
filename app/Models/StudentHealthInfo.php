<?php

namespace App\Models;

ini_set('memory_limit', '4G'); // Set to 4GB, adjust if necessary

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentHealthInfo extends Model
{
    use HasFactory;
    protected $table = 'health_information';
    protected $fillable = [
        'student_id',
        'medical_conditions',
        'allergies',
        'medications',
        'emergency_contact',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function healthInfo()
    {
        return $this->hasMany(StudentHealthInfo::class);
    }
}
