<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;
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
            //'phone_number' => 'required|numeric|unique:users,phone', // إضافة رقم الهاتف
            'phone' => 'required|numeric|unique:users,phone', // تغيير من 'phone_number' إلى 'phone'
            //'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // إنشاء المستخدم في قاعدة البيانات
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => $request->first_name . ' ' . $request->last_name,
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
        // التحقق من صحة البيانات المدخلة
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'phone' => ['required', 'numeric', 'digits:11', 'regex:/^01[0-9]{9}$/'],
        ]);

        // التحقق من وجود المستخدم
        $user = User::where('email', $request->email)
            ->where('phone', $request->phone)
            ->first();

        if ($user) {
            // التحقق من الـ OTP Expiry
            dd($user->otp_expiry);
            if ($user->otp_expiry && now()->diffInSeconds($user->otp_expiry) < 60) {
                return response()->json(['error' => 'Please wait before requesting another OTP.'], 429);
            }

            // توليد OTP جديد
            $otp = rand(1000, 9999);

            // تخزين OTP مشفر
            $user->otp = Hash::make($otp);
            $user->otp_expiry = now()->addMinutes(1); // تعيين وقت انتهاء صلاحية OTP
            $user->save();

            // إرسال OTP عبر البريد الإلكتروني
            try {
                Mail::to($user->email)->send(new OtpMail($otp));
            } catch (\Exception $e) {
                return response()->json(['error' => 'Failed to send email: ' . $e->getMessage()], 500);
            }

            // إرسال OTP عبر الرسائل النصية
            try {
                $this->sendSms($user->phone, "Your OTP is: $otp");
            } catch (\Exception $e) {
                return response()->json(['error' => 'Failed to send SMS: ' . $e->getMessage()], 500);
            }

            return response()->json(['message' => 'OTP sent to your email and phone.']);
        }

        // في حالة عدم العثور على المستخدم
        return response()->json(['error' => 'User not found.'], 404);
    }

    // دالة إرسال الـ SMS ( تخزين OTP مشفر

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
