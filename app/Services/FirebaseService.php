<?php

namespace App\Services;

use App\Models\BusLocation;  // Import the BusLocation model
use Kreait\Firebase\Factory;

class FirebaseService
{
    protected $database;

    public function __construct()
    {
        $serviceAccountPath = storage_path('app/firebase/firebase_credentials.json');
        $databaseUri = 'https://routuss-default-rtdb.firebaseio.com'; 

        $this->database = (new Factory)
            ->withServiceAccount($serviceAccountPath)
            ->withDatabaseUri($databaseUri)
            ->createDatabase();
    }

    // Function to get bus location from Firebase
    public function getBusLocation($busId)
    {
        // Reference to the bus location in Firebase Realtime Database
        $busRef = $this->database->getReference('tracker');
        $busData = $busRef->getValue();

        return $busData;
    }

    // Function to store bus location in MySQL database
    public function storeBusLocationInDB($busId, $latitude, $longitude)
    {
        // Create a new bus location entry
        BusLocation::create([
            'bus_id' => $busId,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'location_timestamp' => now(), // Add this line
        ]);
    }
}
