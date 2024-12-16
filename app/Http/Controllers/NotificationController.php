<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Send an emergency notification
    public function sendEmergencyNotification(Request $request)
    {
        // Validate input
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'target_role' => 'required|in:parent,school,both'
        ]);

        // Create the notification record in the database
        $notification = Notification::create([
            'title' => $request->title,
            'message' => $request->message,
            'target_role' => $request->target_role,
        ]);

        // Retrieve the users to notify
        $query = User::query();

        if ($request->target_role === 'parent') {
            $query->where('role', 'parent');
        } elseif ($request->target_role === 'school') {
            $query->where('role', 'school');
        } else {
            // Send to both parents and school
            $query->whereIn('role', ['parent', 'school']);
        }

        $recipients = $query->get();

        // Send notifications via email (or push notification)
        foreach ($recipients as $user) {
            // Example: Send email to users
            Mail::raw("Emergency: {$notification->message}", function ($message) use ($user, $notification) {
                $message->to($user->email)
                    ->subject("Emergency Notification: {$notification->title}");
            });
        }

        return response()->json(['message' => 'Emergency notification sent successfully'], 200);
    }
}
