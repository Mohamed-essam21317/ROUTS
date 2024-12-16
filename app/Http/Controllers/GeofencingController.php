<?php

namespace App\Http\Controllers;

use App\Models\Geofencing;
use App\Models\Bus;
use Illuminate\Http\Request;

class GeofencingController extends Controller
{
    // Check if a student is inside their geofence
    public function checkStudentGeofence(Request $request, $studentId)
    {
        $geofence = Geofencing::where('student_id', $studentId)->first();
        if (!$geofence) {
            return response()->json(['message' => 'Geofence not found for the student.'], 404);
        }

        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');

        if ($geofence->isInsideGeofence($latitude, $longitude)) {
            return response()->json(['message' => 'The student is inside the geofence.']);
        } else {
            return response()->json(['message' => 'The student is outside the geofence.']);
        }
    }

    // Check if a bus is inside a specific geofence
    public function checkBusGeofence(Request $request, $busId)
    {
        $bus = Bus::findOrFail($busId);

        $latitude = $bus->current_latitude;
        $longitude = $bus->current_longitude;

        $geofenceLatitude = $request->input('geofence_latitude');
        $geofenceLongitude = $request->input('geofence_longitude');
        $geofenceRadius = $request->input('geofence_radius');

        $distance = $this->calculateDistance($latitude, $longitude, $geofenceLatitude, $geofenceLongitude);

        if ($distance <= $geofenceRadius) {
            return response()->json(['message' => 'The bus is inside the geofence.']);
        } else {
            return response()->json(['message' => 'The bus is outside the geofence.']);
        }
    }

    // Calculate distance between two points using the Haversine formula
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;

        $a = sin($dlat / 2) * sin($dlat / 2) +
            cos($lat1) * cos($lat2) *
            sin($dlon / 2) * sin($dlon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $radius = 6371; // Radius of the Earth in km
        return $radius * $c * 1000; // Convert to meters
    }
}
