<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'name',
        'school_id',
        'age',
        'parent_id',
        ' grade',   
        'address',    
    ];
  


    
    public function school()
    {
        return $this->belongsTo(School::class);
    }
    public function child()
    {
        return $this->hasOne(Child::class);
    }
}
