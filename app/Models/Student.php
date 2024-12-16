<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $table = 'students';

    protected $primaryKey = 'id'; // School-provided IDs

    protected $fillable = [
        'id',
        'name',
        'age',
        'parent_id',
        'bus_id',
        'health_info',
        'attendance_status',
    ];

    // Relationship with Parent (Client) - A student belongs to one parent
    public function parent()
    {
        return $this->belongsTo(Client::class, 'parent_id', 'id');
    }

    // Relationship with Bus - A student belongs to one bus
    public function bus()
    {
        return $this->belongsTo(Bus::class, 'bus_id', 'id');
    }

    // Relationship with Geofencing - A student has one geofencing record
    public function geofencing()
    {
        return $this->hasOne(Geofencing::class, 'student_id', 'id');
    }

    // Check if a student is inside their geofence
    public function isInsideGeofence($latitude, $longitude)
    {
        return $this->geofencing && $this->geofencing->isInsideGeofence($latitude, $longitude);
    }
}
