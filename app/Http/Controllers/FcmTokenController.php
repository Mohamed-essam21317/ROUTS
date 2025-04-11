<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FcmToken;
use App\Models\User;

class FcmTokenController extends Controller
{
    
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'fcm_token' => 'required|string|unique:fcm_tokens,fcm_token',
        ]);

        FcmToken::updateOrCreate(
            ['user_id' => $request->user_id],
            ['fcm_token' => $request->fcm_token]
        );

        return response()->json(['message' => ' FCM Token saved successfully!'], 200);
    }
}
