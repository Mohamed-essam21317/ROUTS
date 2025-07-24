<?php

namespace Database\Seeders;

use App\Models\Parents;
use App\Models\Route;
use App\Models\Bus;
use App\Models\BusLocation;
use App\Models\Student;
use App\Models\School;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RouteAndBusSeeder extends Seeder
{
    public function run()
    {
        // Create a school
        $school = School::updateOrCreate(
            ['id' => 2], // Ensure no duplicate school ID
            [
                'school_name' => 'Glory School',
                'school_id' => 'SCH002', // Unique school_id
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Insert driver and supervisor into the clients table
        $driver = DB::table('clients')->updateOrInsert(
            ['role_based_id' => '2'], // Ensure no duplicate driver
            [
                'name' => 'Driver One',
                'email' => 'driver1@example.com',
                'password' => bcrypt('password'),
                'role' => 'Driver',
                'phone_number' => '0123456789',
                'national_id' => '12345678901234',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $supervisor = DB::table('clients')->updateOrInsert(
            ['role_based_id' => '3'], // Ensure no duplicate supervisor
            [
                'name' => 'Supervisor One',
                'email' => 'supervisor1@example.com',
                'password' => bcrypt('password'),
                'role' => 'Supervisor',
                'phone_number' => '0987654321',
                'national_id' => '98765432109876',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Create a route
        $route = Route::updateOrCreate(
            ['route_name' => 'Morning Route 1'], // Ensure no duplicate route
            [
                'start_location' => '30.0444, 31.2357', // Cairo, Egypt
                'end_location' => '30.8418, 31.3276', // Mansoura, Egypt
                'optimized_path' => json_encode([
                    ['lat' => 30.0444, 'lng' => 31.2357],
                    ['lat' => 30.1234, 'lng' => 31.2567],
                    ['lat' => 30.5678, 'lng' => 31.2789],
                    ['lat' => 30.8418, 'lng' => 31.3276]
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Create a bus
        $busId = DB::table('buses')->insertGetId([
            'bus_number' => 'TEST-001',
            'driver_id' => '2', // Ensure this exists in the clients table
            'supervisor_id' => '3', // Ensure this exists in the clients table
            'route_id' => $route->id, // Ensure this exists in the routes table
            'capacity' => 30,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $bus = Bus::find($busId);

        // Create a parent
        $parent = Parents::updateOrCreate(
            ['user_id' => 2], // Ensure no duplicate parent for the same user
            [
                'phone' => '01222222',
                'address' => '123 Test St',
                'gender' => 'female',
                'dob' => '1980-01-01',
                'profile_picture' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Create test students with pickup locations
        $students = [
            [
                'first_name' => 'Mohamed',
                'last_name' => 'Essam',
                'school_id' => $school->id,
                'parent_id' => $parent->id,
                'grade' => 'six',
                'pickup_address' => 'Qanat Elswes',
                'pickup_latitude' => 24.7136, // Example coordinates for Riyadh
                'pickup_longitude' => 46.6753,
                'bus_id' => $bus->id,
                'school_name' => 'Glory School', // Provide a value for school_name
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Ahmed',
                'last_name' => 'Yussef',
                'school_id' => $school->id,
                'parent_id' => $parent->id,
                'grade' => 'seven',
                'pickup_address' => 'Qanat Elswes',
                'pickup_latitude' => 24.7137, // Nearby location
                'pickup_longitude' => 46.6754,
                'bus_id' => $bus->id,
                'school_name' => 'Glory School', // Provide a value for school_name
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        foreach ($students as $studentData) {
            Student::updateOrCreate(
                ['first_name' => $studentData['first_name'], 'last_name' => $studentData['last_name']], // Ensure no duplicate student
                $studentData
            );
        }

        // Create initial bus location
        BusLocation::updateOrCreate(
            ['bus_id' => $bus->id], // Ensure no duplicate bus location
            [
                'latitude' => 24.7135, // Starting point
                'longitude' => 46.6752,
                'speed' => 40,
                'location_timestamp' => now()
            ]
        );
    }
}
