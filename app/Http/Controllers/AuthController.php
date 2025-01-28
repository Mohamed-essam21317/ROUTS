<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Twilio\Rest\Client;

class AuthController extends Controller
{
    public function login(Request $request)
    {
       // dd($request->all());
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
            ]);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    //  تسجيل الخروج
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    //  التسجيل
    public function register(Request $request)
    {
        // التحقق من البيانات المدخلة
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date|before:today',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'phone' => 'required|numeric|unique:users,phone', // إضافة رقم الهاتف
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // إنشاء المستخدم في قاعدة البيانات
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'date_of_birth' => $request->date_of_birth,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone, // حفظ رقم الهاتف
        ]);

        // إنشاء توكن للمستخدم
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    // إرسال الرسائل النصية
    private function sendSms($phone, $message)
    {
        $sid = env('TWILIO_SID');
        $authToken = env('TWILIO_AUTH_TOKEN');
        $twilioPhoneNumber = env('TWILIO_PHONE_NUMBER');

        $client = new Client($sid, $authToken);

        $client->messages->create(
            $phone,
            [
                'from' => $twilioPhoneNumber,
                'body' => $message,
            ]
        );
    }

    // دالة إرسال OTP
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'phone' => 'required|numeric|exists:users,phone',
        ]);

        $user = User::where('email', $request->email)
            ->where('phone', $request->phone)
            ->first();

        if ($user) {
            // تحقق من الطلبات المتكررة
            if ($user->otp_expiry && now()->diffInSeconds($user->otp_expiry) < 60) {
                return response()->json(['error' => 'Please wait before requesting another OTP.'], 429);
            }

            $otp = rand(1000, 9999);

            // تخزين OTP مشفر
            $user->otp = Hash::make($otp);
            $user->otp_expiry = now()->addMinutes(10);
            $user->save();

            // إرسال OTP
            Mail::to($user->email)->send(new OtpMail($otp));
            $this->sendSms($user->phone, "Your OTP is: $otp");

            return response()->json(['message' => 'OTP sent to your email and phone.']);
        }

        return response()->json(['error' => 'User not found.'], 404);
    }

    // دالة التحقق من OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|numeric|digits:4',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->otp, $user->otp) && now()->lt($user->otp_expiry)) {
            $user->otp = null;
            $user->otp_expiry = null;
            $user->save();

            return response()->json(['message' => 'OTP verified. Please set your new password.']);
        }

        return response()->json(['error' => 'Invalid or expired OTP.'], 400);
    }

    // دالة إعادة تعيين كلمة المرور
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();

            return response()->json(['message' => 'Password has been reset successfully.']);
        }

        return response()->json(['error' => 'User not found.'], 404);
    }
}
