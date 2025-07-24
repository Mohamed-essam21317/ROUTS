<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ParentProfile;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ParentProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $profile = $user->parentProfile ?? $user->parentProfile()->create([]);

        return response()->json([
            'user' => [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone' => $user->phone,
            ],
            'profile' => $profile
        ]);
    }

    public function update(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $profile = $user->parentProfile ?? $user->parentProfile()->create([]);

        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string',
            'gender' => 'nullable|in:male,female',
            'date_of_birth' => 'nullable|date',
            'profile_picture' => 'nullable|string',
        ]);

        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone_number,
        ]);

        $profile->update($request->only(['phone_number', 'gender', 'date_of_birth', 'profile_picture']));

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user,
            'profile' => $profile
        ]);
    }

    public function destroy(Request $request)
    {
        // تحديد المستخدم يدويًا من التوكن
        $user = \Laravel\Sanctum\PersonalAccessToken::findToken($request->bearerToken())?->tokenable;
        if ($user) {
            Auth::login($user);
        }

        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if ($user->parentProfile) {
            $user->parentProfile->delete();
        }

        // $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();
        // $user->delete();

        return response()->json(['message' => 'Parent account deleted successfully']);
    }
}
