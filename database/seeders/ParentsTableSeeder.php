<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParentsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('parents')->insert([
            [
                'user_id' => 1, // Ensure this user_id exists in the users table
                'phone' => '01111111111',
                'gender' => 'Male',
                'dob' => '1980-01-01',
                'profile_picture' => null,
                'address' => '123 Test St',
                'fcm_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
