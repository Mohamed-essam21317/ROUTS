<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Otp;
use Illuminate\Support\Facades\Mail;
use App\Mail\OTPMail;
use Twilio\Rest\Client;

class OTPController extends Controller
{
    // إرسال OTP
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
        ]);

        if (!$request->email && !$request->phone) {
            return response()->json(['message' => 'Please provide either email or phone'], 400);
        }

        $otp = rand(1000, 9999);

        if ($request->email) {
            Mail::to($request->email)->send(new OTPMail($otp));
        }

        if ($request->phone) {
            $this->sendSmsOTP($request->phone, $otp);
        }

        Otp::create([
            'email' => $request->email,
            'phone' => $request->phone,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(5),
        ]);

        return response()->json(['message' => 'OTP sent successfully']);
    }

    // إرسال OTP عبر SMS باستخدام Twilio
    private function sendSmsOTP($phoneNumber, $otp)
    {
        $twilioSid = env('TWILIO_SID');
        $twilioAuthToken = env('TWILIO_AUTH_TOKEN');
        $twilioPhoneNumber = env('TWILIO_PHONE_NUMBER');

        $client = new Client($twilioSid, $twilioAuthToken);

        $client->messages->create(
            $phoneNumber,
            [
                'from' => $twilioPhoneNumber,
                'body' => "Your OTP code is: $otp",
            ]
        );
    }

    // التحقق من OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'otp' => 'required|digits:4',
        ]);

        // البحث عن الكود في قاعدة البيانات
        $otpRecord = Otp::where(function ($query) use ($request) {
            $query->where('email', $request->email)
                ->orWhere('phone', $request->phone);
        })->where('otp', $request->otp)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otpRecord) {
            return response()->json(['message' => 'Invalid or expired OTP'], 400);
        }

        // حذف الكود بعد التحقق
        $otpRecord->delete();

        return response()->json(['message' => 'OTP verified successfully']);
    }
}
