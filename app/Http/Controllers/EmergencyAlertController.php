<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\EmergencyAlert;
use Illuminate\Support\Facades\Auth;
use App\Notifications\EmergencyAlertNotification;

class EmergencyAlertController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'alert_type' => 'required|in:general,accident,medical',
        ]);

        $supervisor = Auth::user();
        $schoolId = $supervisor->school_id;

        // Create the emergency alert
        $alert = EmergencyAlert::create([
            'user_id' => $supervisor->id,
            'school_id' => $schoolId,
            'alert_type' => $request->alert_type,
        ]);

        // Get all admins for the same school
        $admins = Admin::where('school_id', $schoolId)->get();


        foreach ($admins as $admin) {
            $admin->notify(new EmergencyAlertNotification($alert));
        }

        return response()->json([
            'message' => 'Emergency alert sent to school admins.',
            'alert' => $alert,
            'admin_count' => $admins->count(),
        ]);
    }
}
