<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Child extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'school_id', 'student_id', 'parent_id'];

    
    public function school()
{
    return $this->belongsTo(School::class);
}

 
    public function student()
{
    return $this->belongsTo(Student::class);
}
}

