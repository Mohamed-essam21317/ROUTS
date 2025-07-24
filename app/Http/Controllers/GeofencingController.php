<?php

namespace App\Http\Controllers;


use App\Models\Bus;
use App\Models\Geofencing;
use App\Models\Geofence; // Ensure this model exists in the specified namespace
use App\Models\Student; // Import the Student model
use Illuminate\Http\Request;
use App\Services\GeofencingService;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Models\Notification; // Import the Notification model
use App\Events\BusProximityEvent; // Import the BusProximityEvent class

class GeofencingController extends Controller
{
    private $geofencingService;

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

    // private function notifyParents($student)
    // {
    //     // Example logic to notify parents
    //     foreach ($student->parentNotifications as $parent) {
    //         Log::info("Notification sent to parent ID: {$parent->id} for student ID: {$student->id}");
    //         // Add actual notification logic here (e.g., email, SMS, etc.)
    //     }
    // }


    private function notifyParents($student)
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
        $response = $fcmService->sendNotification($parent->fcm_token, $title, $body);

        // Log the response
        Log::info("Notification sent to parent of student: {$student->id}", ['response' => $response]);
    }

    public function testGeofenceEvent(Request $request)
    {
        $studentId = $request->input('student_id');
        $event = $request->input('event'); // e.g., 'enter' or 'exit'

        $student = Student::find($studentId); // Retrieve the student record

        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $this->notifyParents($student); // Notify parents about the geofence event

        // Log or handle the event type if needed
        Log::info("Geofence event: {$event} for student ID: {$studentId}");
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $this->notifyParents($student); // this calls your actual logic

        return response()->json([
            'status' => 'success',
            'message' => "Geofence event '{$event}' simulated and notification sent."
        ]);
    }



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


    public function __construct(GeofencingService $geofencingService)
    {
        $this->geofencingService = $geofencingService;
    }

    public function checkBusLocation(Request $request)
    {
        try {
            $request->validate([
                'bus_id' => 'required|integer',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric'
            ]);

            $busId = $request->input('bus_id');
            $latitude = $request->input('latitude');
            $longitude = $request->input('longitude');

            // Update bus location first
            $bus = Bus::find($busId);
            if (!$bus) {
                return response()->json(['error' => 'Bus not found'], 404);
            }

            // Update bus location in the database
            $bus->update([
                'current_latitude' => $latitude,
                'current_longitude' => $longitude
            ]);

            // Use enhanced geofencing service to check proximity and send notifications
            $enhancedService = new \App\Services\EnhancedGeofencingService();
            $result = $enhancedService->checkBusStudentProximity($busId, $latitude, $longitude);

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error("Error in checkBusLocation: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error checking bus location: ' . $e->getMessage()
            ], 500);
        }
    }


    public function checkProximity(Request $request, $busId)
    {
        try {
            // Fetch the latest bus location from the bus_locations table
            $busLocation = \App\Models\BusLocation::where('bus_id', $busId)
                ->orderBy('location_timestamp', 'desc')
                ->first();

            if (!$busLocation) {
                return response()->json(['error' => 'Bus location not found'], 404);
            }

            $busLatitude = $busLocation->latitude;
            $busLongitude = $busLocation->longitude;

            // Use enhanced geofencing service
            $enhancedService = new \App\Services\EnhancedGeofencingService();
            $result = $enhancedService->checkBusStudentProximity($busId, $busLatitude, $busLongitude);

            return response()->json($result);

        } catch (\Exception $e) {
            \Log::error("Error in checkProximity: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error checking proximity: ' . $e->getMessage()
            ], 500);
        }
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

    // Removed duplicate notifyParentOfStudent method to resolve duplicate symbol error.

    /**
     * Notify the parent of a student and return true if notification was sent, false otherwise.
     */
    // private function notifyParent($student)
    // {
    //     $parent = $student->parent;
    //     if (!$parent) {
    //         Log::warning("Parent not found for student: {$student->id}");
    //         return false;
    //     }

    //     $message = "The bus is near your child's pickup location: {$student->first_name} {$student->last_name}.";
    //     try {
    //         $this->sendNotification($parent, $message);
    //         return true;
    //     } catch (Exception $e) {
    //         Log::error("Failed to notify parent (ID: {$parent->id}) for student (ID: {$student->id}): " . $e->getMessage());
    //         return false;
    //     }
    // }




    public function notifyParentOfStudent($studentId)
    {
        $student = Student::find($studentId);
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        // Adjust this according to your schema
        $parent = $student->parent; // or User::find($student->parent_id);
        if (!$parent) {
            return response()->json(['error' => 'Parent not found'], 404);
        }

        DB::table('notifications')->insert([
            'user_id'    => $parent->id,
            'message'    => "The bus is near your child's pickup location: {$student->first_name} {$student->last_name}.",
            'type'       => 'Emergency',
            'status'     => 'Unread',
            'log'        => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['status' => 'success', 'message' => 'Parent notified.']);
    }
    // private function sendNotification($parent, $message)
    // {
    //     // Example: Store notification in the database
    //     Notification::create([
    //         'user_id' => $parent->id,
    //         'message' => $message,
    //         'type' => 'bus_proximity'
    //     ]);

    //     // Example: Send real-time notification using Firebase or Pusher
    //     event(new BusProximityEvent($parent->id, $message));
    // }

    public function create(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'radius' => 'required|numeric'
            ]);

            // Enable query log
            DB::enableQueryLog();

            // Insert into database
            $inserted = DB::table('geofencing')->insert([
                'name' => $validated['name'],
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'radius' => $validated['radius'],
                'student_id' => $request->input('student_id'),
                'bus_id' => $request->input('bus_id'),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Log query for debugging
            Log::info('Geofence Insert Query:', DB::getQueryLog());

            return response()->json([
                'success' => $inserted,
                'message' => $inserted ? 'Geofence created successfully' : 'Failed to create geofence'
            ]);
        } catch (Exception $e) {
            Log::error('Geofence Insertion Error:', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error creating geofence',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
