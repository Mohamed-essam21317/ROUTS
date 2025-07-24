<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Services\GeofencingService;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\Student;
use App\Events\BusProximityEvent;
use Illuminate\Support\Facades\Log;

event(new BusProximityEvent($parent->id, "The bus is near your child's location."));


class TestGeofencingController extends Controller



{
    private $geofencingService;


    public function __construct(GeofencingService $geofencingService)
    {
        $this->geofencingService = $geofencingService;
    }

    public function simulateMovement(Request $request, Bus $bus)
    {
        // Validate the request
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        // Get the current location of the bus
        $currentLocation = $bus->currentLocation;

        if (!$currentLocation) {
            return response()->json(['error' => 'No initial location found'], 404);
        }

        // Move bus slightly closer to first student
        $newLatitude = $currentLocation->latitude + 0.0001; // Small increment
        $newLongitude = $currentLocation->longitude + 0.0001;

        // Create new location
        $location = $bus->locations()->create([
            'latitude' => $newLatitude,
            'longitude' => $newLongitude,
            'speed' => 30,
            'location_timestamp' => now()
        ]);

        // Check proximity and trigger notifications
        $this->geofencingService->checkProximity($bus, $newLatitude, $newLongitude);

        return response()->json([
            'message' => 'Bus movement simulated',
            'location' => $location,
            'next_coordinates' => [
                'latitude' => $newLatitude,
                'longitude' => $newLongitude
            ]
        ]);
    }

    public function testNotification(Request $request)
    {
        $fcmService = new \App\Services\FCMService();

        // return $fcmService->send(
        //     $request->input('fcm_token'),
        //     'Test Notification',
        //     'This is a test notification from the school bus system',
        //     ['type' => 'test']
        // );
    }

    // public function checkProximity(Bus $bus)
    // {
    //     $latitude = $bus->currentLocation->latitude;
    //     $longitude = $bus->currentLocation->longitude;
    //     $this->geofencingService->checkProximity($bus, $latitude, $longitude);

    //     return response()->json(['status' => 'success', 'message' => 'Proximity check completed.']);
    // }


    // public function checkProximity(Bus $bus)
    // {
    public function checkProximity(Request $request, $busId)
    {
        // Validate the request
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');

        // Fetch the bus
        $bus = Bus::find($busId);
        if (!$bus) {
            return response()->json(['error' => 'Bus not found'], 404);
        }

        // Fetch students assigned to the bus
        $students = Student::where('bus_id', $bus->id)->with('geofencing')->get();

        foreach ($students as $student) {
            $geofence = $student->geofencing;
            if (!$geofence) {
                continue;
            }

            // Calculate the distance between the bus and the student's geofence
            $distance = $this->calculateDistance(
                $latitude,
                $longitude,
                $geofence->latitude,
                $geofence->longitude
            );

            // Check if the bus is within the geofence radius
            if ($distance <= $geofence->radius) {
                // Notify the parent
                $this->notifyParent($student);
            }
        }

        return response()->json(['status' => 'success', 'message' => 'Proximity check completed.']);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Radius of the Earth in kilometers

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c * 1000; // Convert to meters
    }


    private function notifyParent($student)
    {
        // Fetch the parent of the student
        $parent = $student->parent;

        if (!$parent || !$parent->fcm_token) {
            Log::warning("Parent or FCM token not found for student: {$student->id}");
            return;
        }

        // Prepare the notification data
        $title = "Bus Approaching";
        $body = "The bus is near your child's location: {$student->first_name} {$student->last_name}.";
        $data = [
            'type' => 'bus_proximity',
            'student_id' => $student->id,
            'bus_id' => $student->bus_id,
        ];

        // Send the notification via FCM
        $fcmService = new \App\Services\FCMService();
        // $response = $fcmService->send($parent->fcm_token, $title, $body, $data);

        // Log the response
        // Log::info("Notification sent to parent of student: {$student->id}", ['response' => $response]);
    }
    /**
     * Function to send a notification to a parent
     */
    private function sendNotification($parent, $message)
    {
        // Example: Store notification in the database
        Notification::create([
            'user_id' => $parent->id,
            'message' => $message,
            'type' => 'bus_proximity'
        ]);

        // Example: Send real-time notification using Firebase or Pusher
        event(new BusProximityEvent($parent->id, $message));
    }
}
