<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('students')->insert([
            [
                'first_name' => 'Mohamed',
                'last_name' => 'Essam',
                'school_id' => 1,
                'parent_id' => 1,
                'grade' => 'six',
                'pickup_address' => 'Qanat Elswes',
                'pickup_latitude' => 24.7136,
                'pickup_longitude' => 46.6753,
                'bus_id' => 1,
                'school_name' => 'Glory School', // Provide a value for school_name
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Ahmed',
                'last_name' => 'Yussef',
                'school_id' => 1,
                'parent_id' => 1,
                'grade' => 'seven',
                'pickup_address' => 'Qanat Elswes',
                'pickup_latitude' => 24.7137,
                'pickup_longitude' => 46.6754,
                'bus_id' => 1,
                'school_name' => 'Glory School', // Provide a value for school_name
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
