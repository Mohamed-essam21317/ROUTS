<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use App\Models\BusLocation;
use Illuminate\Http\Request;

class BusLocationController extends Controller
{
    /**
     * Get the bus location from Firebase and store it in the MySQL database.
     *
     * @param string $busId
     * @param FirebaseService $firebaseService
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBusLocation($busId, FirebaseService $firebaseService)
    {
        $busData = $firebaseService->getBusLocation($busId);

        if ($busData) {
            $latitude = $busData['latitude'];
            $longitude = $busData['longitude'];

            $this->storeBusLocationInDB($busId, $latitude, $longitude);

            return response()->json([
                'status' => 'success',
                'data' => $busData
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Bus not found'
            ]);
        }
    }

    /**
     * Store the bus location in the backend database (MySQL).
     *
     * @param string $busId
     * @param float $latitude
     * @param float $longitude
     * @return void
     */
    private function storeBusLocationInDB($busId, $latitude, $longitude)
    {
        $location = BusLocation::where('bus_id', $busId)->latest()->first();
        if ($location) {
            $location->update([
                'latitude' => $latitude,
                'longitude' => $longitude,
                'location_timestamp' => now(),
            ]);
        } else {
            BusLocation::create([
                'bus_id' => $busId,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'location_timestamp' => now(),
            ]);
        }
    }
}
