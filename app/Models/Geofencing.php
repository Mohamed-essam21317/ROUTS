<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Geofencing extends Model
{
    use HasFactory;

    protected $table = 'geofencing';

    protected $fillable = [
        'student_id',
        'latitude',
        'longitude',
        'geofence_radius',
    ];

    // Relationship with Student model
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }

    // Calculate the distance between two geographical points using the Haversine formula
    public function calculateDistance($lat1, $lon1)
    {
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($this->latitude);
        $lon2 = deg2rad($this->longitude);

        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;

        $a = sin($dlat / 2) * sin($dlat / 2) +
            cos($lat1) * cos($lat2) *
            sin($dlon / 2) * sin($dlon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $radius = 6371; // Radius of the Earth in km
        $distance = $radius * $c;

        return $distance * 1000; // Return distance in meters
    }

    // Check if the given location is inside the geofence
    public function isInsideGeofence($latitude, $longitude)
    {
        $distance = $this->calculateDistance($latitude, $longitude);
        return $distance <= $this->geofence_radius;
    }
}
