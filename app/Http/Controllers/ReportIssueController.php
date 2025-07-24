<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportIssueController extends Controller
{
    protected $fcmService;

    public function __construct(\App\Services\FcmService $fcmService)
    {
        $this->fcmService = $fcmService;
    }

    public function report(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        // 1. Check if the user is a supervisor
        $supervisor = \App\Models\User::where('id', $request->user_id)
            ->where('role', 'supervisor')
            ->first();

        if (!$supervisor) {
            return response()->json([
                'status' => 'error',
                'message' => 'Only supervisors can report issues.',
            ], 403);
        }

        // 2. Find all admins for the same school
        $admins = \App\Models\User::where('school_id', $supervisor->school_id)
            ->where('role', 'admin')
            ->whereNotNull('fcm_token')
            ->get();

        if ($admins->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No admins with FCM tokens found for this school.',
            ], 404);
        }

        // 3. Send FCM notification to all admins
        $tokens = $admins->pluck('fcm_token')->toArray();
        $title = "Issue Reported by Supervisor";
        $body = $request->message;

        $this->fcmService->sendNotification($tokens, $title, $body);

        return response()->json([
            'status' => 'success',
            'message' => 'Issue reported and notification sent to all admins.',
        ]);
    }
}
