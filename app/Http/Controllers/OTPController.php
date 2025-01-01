<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\OTPMail;
use App\Helpers\OTPHelper;
use Illuminate\Support\Facades\Mail;
use Twilio\Rest\Client;

class OTPController extends Controller
{
    public function sendOTP(Request $request): \Illuminate\Http\JsonResponse
    {
        $otp = OTPHelper::generateOTP();

        // Send OTP via email
        if (isset($request->email)) {
            Mail::to($request->email)->send(new OTPMail($otp));
        }

        // Send OTP via SMS (Twilio)
        if (isset($request->phone)) {
            $this->sendOTPBySMS($request->phone, $otp);
        }

        return response()->json(['message' => 'OTP sent successfully']);
    }

    private function sendOTPBySMS($phone, $otp)
    {
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $twilio = new Client($sid, $token);

        $twilio->messages->create(
            $phone,
            [
                'from' => env('TWILIO_PHONE_NUMBER'),
                'body' => "Your OTP code is: $otp"
            ]
        );
    }
}

