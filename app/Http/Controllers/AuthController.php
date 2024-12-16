<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
class AuthController extends Controller
{
    // Method to send OTP
    public function sendOtp(Request $request)
    {
        // Validate the email input
        $request->validate([
            'email' => 'required|email'
        ]);

        // Find user by email
        $user = User::where('email', $request->email)->first();

        // If user doesn't exist, return an error
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Generate a 6-digit OTP
        $otp = mt_rand(100000, 999999);

        // Save OTP and its expiration time in the database
        $user->otp = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(5); // OTP valid for 5 minutes
        $user->save();

        // Send OTP via email
        Mail::raw("Your OTP code is: $otp", function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Your OTP Code');
        });

        // Return a success response
        return response()->json(['message' => 'OTP sent successfully'], 200);
    }


    public function verifyOtpAndSetPassword(Request $request)
    {
        // Validate input
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Find the user by email
        $user = User::where('email', $request->email)->first();

        // Check if user exists
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Verify OTP and check expiry
        if ($user->otp == $request->otp && now()->lt($user->otp_expires_at)) {
            // OTP is valid, update the password
            $user->password = Hash::make($request->password); // Hash the new password
            $user->otp = null; // Clear OTP after successful verification
            $user->otp_expires_at = null;
            $user->save();

            return response()->json(['message' => 'Password updated successfully'], 200);
        }

        // OTP is invalid or expired
        return response()->json(['message' => 'Invalid or expired OTP'], 400);
    }
}
