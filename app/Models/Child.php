<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Child extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'school_id', 'student_id', 'parent_id'];

    // علاقة مع المدارس
    public function school()
{
    return $this->belongsTo(School::class);
}

    // علاقة مع الطالب
    public function student()
{
    return $this->belongsTo(Student::class);
}
}

