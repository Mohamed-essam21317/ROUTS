<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    // إضافة الأعمدة الجديدة إلى الـ fillable
    protected $fillable = [
        'name',
        'school_id',
        'age',
        'parent_id',
        'academic_year',   // السنة الدراسية
        'access_point',    // نقطة الوصول
    ];

    // إضافة العلاقة مع مدرسة (إذا كنت عامل علاقة BelongsTo)
    public function school()
    {
        return $this->belongsTo(School::class);
    }
    public function child()
    {
        return $this->hasOne(Child::class);
    }
}
