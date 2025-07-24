<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmergencyAlert;
use App\Models\Admin;
use Illuminate\Support\Facades\Http;
use App\Helpers\FCMHelper;

class FCMController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'alert_type' => 'required|in:accident,medical,general',
        ]);

        $user = $request->user();

        if ($user->role !== 'supervisor') {
            return response()->json(['message' => 'Only supervisors can send alerts.'], 403);
        }

        // Store the alert
        $alert = EmergencyAlert::create([
            'user_id' => $user->id,
            'alert_type' => $request->alert_type,
        ]);

        // Get FCM access token
        $accessToken = FCMHelper::getAccessToken();
        // $projectId = env('FIREBASE_PROJECT_ID');

        // Get admins from same school
        $admins = Admin::where('school_id', $user->school_id)
            ->whereNotNull('fcm_token')
            ->get();

        foreach ($admins as $admin) {
            $payload = [
                'message' => [
                    'token' => $admin->fcm_token,
                    'notification' => [
                        'title' => '🚨 Emergency Alert',
                        'body' => ucfirst($alert->alert_type) . ' reported by Supervisor ' . $user->name,
                    ],
                    'data' => [
                        'type' => 'emergency_alert',
                        'alert_type' => $alert->alert_type,
                        'from' => $user->name,
                    ]
                ]
            ];

            Http::withToken($accessToken)
                ->post("https://fcm.googleapis.com/v1/projects/routus-2e2cd/messages:send", $payload);
        }

        return response()->json([
            'message' => 'Emergency alert sent to school admin(s).',
            'alert' => $alert
        ]);
    }

    public function storeToken(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'fcm_token' => 'required|string',
        ]);

        // Update the user's FCM token in the database
        $user = \App\Models\User::find($request->user_id);
        $user->fcm_token = $request->fcm_token;
        $user->save();

        return response()->json(['message' => 'FCM token stored successfully.']);
    }
}
