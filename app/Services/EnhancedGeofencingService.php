<?php

namespace App\Services;

use App\Models\Bus;
use App\Models\Student;
use App\Models\Parents;
use App\Models\Geofencing;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class EnhancedGeofencingService
{
    /**
     * Calculate the distance between two geographical points using the Haversine formula.
     *
     * @param float $lat1 Latitude of point 1
     * @param float $lon1 Longitude of point 1
     * @param float $lat2 Latitude of point 2
     * @param float $lon2 Longitude of point 2
     * @return float Distance in meters
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meters
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $dLat = $lat2 - $lat1;
        $dLon = $lon2 - $lon1;

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos($lat1) * cos($lat2) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Check proximity between bus and assigned students
     * Send notifications to parents when bus enters student's geofencing area
     */
    public function checkBusStudentProximity($busId, $busLatitude, $busLongitude)
    {
        try {
            // Get the bus
            $bus = Bus::find($busId);
            if (!$bus) {
                Log::error("Bus not found with ID: {$busId}");
                return [
                    'status' => 'error',
                    'message' => 'Bus not found'
                ];
            }

            // Get all students assigned to this bus
            $students = Student::where('bus_id', $busId)->get();
            
            if ($students->isEmpty()) {
                Log::info("No students assigned to bus ID: {$busId}");
                return [
                    'status' => 'success',
                    'message' => 'No students assigned to this bus',
                    'notifications_sent' => 0
                ];
            }

            $notificationsSent = 0;
            $results = [];

            foreach ($students as $student) {
                $result = $this->checkStudentProximity($student, $busLatitude, $busLongitude, $bus);
                $results[] = $result;
                
                if ($result['notification_sent']) {
                    $notificationsSent++;
                }
            }

            return [
                'status' => 'success',
                'message' => 'Proximity check completed',
                'notifications_sent' => $notificationsSent,
                'total_students' => $students->count(),
                'results' => $results
            ];

        } catch (\Exception $e) {
            Log::error("Error in checkBusStudentProximity: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Error checking proximity: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Check proximity for a specific student
     */
    private function checkStudentProximity($student, $busLatitude, $busLongitude, $bus)
    {
        $studentLatitude = $student->pickup_latitude;
        $studentLongitude = $student->pickup_longitude;
        
        // Default geofence radius (100 meters) if no specific geofence is set
        $geofenceRadius = 100;
        
        // Check if there's a specific geofence for this student
        $geofence = Geofencing::where('student_id', $student->id)->first();
        if ($geofence) {
            $studentLatitude = $geofence->latitude;
            $studentLongitude = $geofence->longitude;
            $geofenceRadius = $geofence->radius;
        }

        // Calculate distance between bus and student pickup location
        $distance = $this->calculateDistance(
            $busLatitude,
            $busLongitude,
            $studentLatitude,
            $studentLongitude
        );

        $isWithinGeofence = $distance <= $geofenceRadius;
        $notificationSent = false;

        if ($isWithinGeofence) {
            $notificationSent = $this->sendProximityNotification($student, $bus, $distance);
        }

        return [
            'student_id' => $student->id,
            'student_name' => $student->first_name . ' ' . $student->last_name,
            'distance' => round($distance, 2),
            'geofence_radius' => $geofenceRadius,
            'is_within_geofence' => $isWithinGeofence,
            'notification_sent' => $notificationSent,
            'pickup_location' => [
                'latitude' => $studentLatitude,
                'longitude' => $studentLongitude,
                'address' => $student->pickup_address
            ]
        ];
    }

    /**
     * Send proximity notification to parent using NotificationController logic
     */
    private function sendProximityNotification($student, $bus, $distance)
    {
        try {
            // Get the parent
            $parent = Parents::find($student->parent_id);
            if (!$parent) {
                Log::warning("Parent not found for student ID: {$student->id}");
                return false;
            }

            // Prepare notification data
            $title = "Bus Approaching Your Location";
            $body = "Your bus (Bus #{$bus->bus_number}) is approaching your child {$student->first_name} {$student->last_name}'s pickup location. Distance: " . round($distance, 0) . " meters.";

            // Add notification to database using the same logic as NotificationController
            DB::table('notifications')->insert([
                'title'        => $title,
                'body'         => $body,
                'model_type'   => 'Geofencing',
                'read_at'      => null,
                'date'         => now()->toDateString(),
                'time'         => now()->toTimeString(),
                'student_id'   => $student->id,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            // Send FCM notification if parent has FCM token
            if ($parent->fcm_token) {
                $this->sendFCMNotification($parent->fcm_token, $title, $body);
            }

            Log::info("Proximity notification sent for student ID: {$student->id}, parent ID: {$parent->id}");
            return true;

        } catch (\Exception $e) {
            Log::error("Error sending proximity notification for student ID {$student->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send FCM notification
     */
    private function sendFCMNotification($fcmToken, $title, $body)
    {
        try {
            $fcmService = new FCMService();
            $response = $fcmService->sendNotification($fcmToken, $title, $body);
            
            Log::info("FCM notification sent", [
                'fcm_token' => $fcmToken,
                'title' => $title,
                'response' => $response
            ]);
            
            return $response;
        } catch (\Exception $e) {
            Log::error("Error sending FCM notification: " . $e->getMessage());
            return false;
        }
    }
} 