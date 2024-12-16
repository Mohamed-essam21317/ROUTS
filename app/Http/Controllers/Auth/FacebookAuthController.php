<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Facebook\Facebook;
use App\Models\User;

class FacebookAuthController extends Controller
{
    protected $facebook;

    public function __construct()
    {
        // Initialize the Facebook SDK
        $this->facebook = new Facebook([
            'app_id' => env('FACEBOOK_APP_ID'),
            'app_secret' => env('FACEBOOK_APP_SECRET'),
            'default_graph_version' => 'v14.0',
        ]);
    }
    public function facebookLogin(Request $request)
    {
        // Retrieve the access token from the request (sent from Flutter app)
        $accessToken = $request->input('access_token');

        try {
            // Use the access token to fetch user details from Facebook
            $response = $this->facebook->get('/me?fields=id,name,email', $accessToken);
            $facebookUser = $response->getGraphUser();  // Get user data

            // Check if the user exists in the database, else create a new user
            $user = User::updateOrCreate(
                ['facebook_id' => $facebookUser['id']], // Check if the Facebook ID already exists
                [
                    'name' => $facebookUser['name'],    // Store name
                    'email' => $facebookUser['email'],  // Store email
                    'facebook_token' => $accessToken,   // Store access token for future API calls if needed
                ]
            );

            // Log the user in
            auth()->login($user);

            // Optionally, generate a JWT or Laravel session here to authenticate the user
            $token = auth()->user()->createToken('YourAppName')->plainTextToken;

            // Return success response with user data and token
            return response()->json([
                'status' => 'success',
                'message' => 'User logged in successfully',
                'user' => $user,
                'token' => $token,  // You can return a JWT token here if you are using API-based authentication
            ], 200);

        } catch (\Exception $e) {
            // Handle any errors that occur during the Facebook authentication process
            return response()->json([
                'status' => 'error',
                'message' => 'Error logging in with Facebook: ' . $e->getMessage(),
            ], 400);
        }
    }


}
