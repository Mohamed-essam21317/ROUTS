<?php

namespace App\Http\Controllers\Api\Supervisor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SchoolPlan;
use App\Http\Resources\SchoolPlanResource;

class SchoolPlanController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->query('month', now()->format('Y-m'));

        $plans = SchoolPlan::whereMonth('start_date', date('m', strtotime($month)))
            ->whereYear('start_date', date('Y', strtotime($month)))
            ->get();

        return SchoolPlanResource::collection($plans);
    }
}

