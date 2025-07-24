<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SchoolsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('schools')->insert([
            [
                'id' => 1,
                'school_name' => 'Gloryy School',
                'school_id' => 'SCH001',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
