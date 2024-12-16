<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google_Client;
use App\Models\User;

class GoogleAuthController extends Controller
{
    public function googleLogin(Request $request)
    {
        $idToken = $request->input('id_token'); // Token sent from Flutter

        $client = new Google_Client(['client_id' => env('GOOGLE_CLIENT_ID')]); // Set client ID
        $payload = $client->verifyIdToken($idToken);

        if ($payload) {
            // Find or create the user
            $user = User::updateOrCreate(
                ['google_id' => $payload['sub']],
                [
                    'name' => $payload['name'],
                    'email' => $payload['email'],
                    'google_id' => $payload['sub'],
                ]
            );

            // Log in the user and create a token
            auth()->login($user);
            $token = auth()->user()->createToken('YourAppName')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'user' => $user,
                'token' => $token,
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid Google token',
            ], 401);
        }
    }
}

