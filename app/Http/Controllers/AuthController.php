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
    if (!Auth::user()) {
        return response()->json(['message' => 'User not authenticated'], 401);
    }

    Auth::user()->tokens()->delete();

    return response()->json(['message' => 'Logged out successfully']);
}

    
    //  التسجيل
    public function register(Request $request)
{
    $validator = Validator::make($request->all(), [
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'date_of_birth' => 'nullable|date|before:today',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6|confirmed',
        'phone' => 'required|numeric|unique:users,phone',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 400);
    }

    try {
        \DB::beginTransaction(); // بدء المعاملة

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => $request->first_name . ' ' . $request->last_name,
            'date_of_birth' => $request->date_of_birth,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
        ]);



     $token = $user->createToken('auth_token')->plainTextToken;

        \DB::commit(); // تأكيد العملية

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    } catch (\Exception $e) {
        \DB::rollBack(); // إلغاء التغييرات إذا حصل خطأ
        return response()->json(['error' => 'Something went wrong.'], 500);
    }
}


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

public function forgotPassword(Request $request)
{
 
    $request->validate([
        'email' => 'required|email|exists:users,email',
        
    ]);

    
    $user = User::where('email', $request->email)
       
        ->first();

    if ($user) {
        
        if ($user->otp_expiry && now()->lt($user->otp_expiry)) {
            return response()->json(['error' => 'Please wait before requesting another OTP.'], 429);
        }

       
        $otp = rand(1000, 9999);

       
        $user->otp = Hash::make($otp);
        $user->otp_expiry = now()->addMinutes(5);
        $user->save();

        
        try {
            Mail::to($user->email)->send(new OtpMail($otp));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to send email: ' . $e->getMessage()], 500);
        }

        return response()->json(['message' => 'OTP sent to your email.']);
    }

    
    return response()->json(['error' => 'User not found.'], 404);
}
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
