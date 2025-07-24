<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StatusController extends Controller
{
    public function getByBus($id)
    {
        $busStatuses = [
            1 => [
                [
                    'time' => '7:45',
                    'title' => 'Pick up',
                    'description' => 'Child picked up safely',
                ],
                [
                    'time' => '8:00',
                    'title' => 'At School',
                    'description' => 'Child is at school',
                ],
                [
                    'time' => '2:30',
                    'title' => 'Drop off',
                    'description' => 'Child dropped off at destination',
                ],
            ],
            2 => [
                [
                    'time' => '7:30',
                    'title' => 'Pick up',
                    'description' => 'Picked up from location A',
                ],
                [
                    'time' => '8:15',
                    'title' => 'At School',
                    'description' => 'Arrived at school B',
                ],
                [
                    'time' => '2:00',
                    'title' => 'Drop off',
                    'description' => 'Dropped off at home',
                ],
            ],
        ];

        if (!isset($busStatuses[$id])) {
            return response()->json(['message' => 'Bus not found'], 404);
        }

        return response()->json($busStatuses[$id]);
    }
}
