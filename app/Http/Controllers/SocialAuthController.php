<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;

class SocialAuthController extends Controller
{
    protected $facebook;

    public function __construct()
    {
        // Initialize the Facebook SDK with your app credentials
        $this->facebook = new Facebook([
            'app_id' => env('FACEBOOK_APP_ID'),         // Facebook App ID from .env
            'app_secret' => env('FACEBOOK_APP_SECRET'), // Facebook App Secret from .env
            'default_graph_version' => 'v16.0',
        ]);
    }

    // Handles Facebook Login from the Flutter app
    public function facebookLogin(Request $request)
    {
        // Retrieve the access token from the request (sent from the Flutter app)
        $accessToken = $request->input('access_token');

        try {
            // Validate the access token with the Facebook API
            $response = $this->facebook->get('/me?fields=id,name,email', $accessToken);
            $facebookUser = $response->getGraphUser(); // Get user data

            // Check if the user exists in the database, or create a new user
            $user = User::updateOrCreate(
                ['facebook_id' => $facebookUser['id']], // Match by Facebook ID
                [
                    'name' => $facebookUser['name'],
                    'email' => $facebookUser['email'],
                    'facebook_token' => $accessToken,
                ]
            );

            // Log the user in
            auth()->login($user);

            // Generate a JWT token or use Laravel's session for authentication
            $token = auth()->user()->createToken('YourAppName')->plainTextToken;

            // Return a success response with user data and token
            return response()->json([
                'status' => 'success',
                'message' => 'User logged in successfully',
                'user' => $user,
                'token' => $token,
            ], 200);

        } catch (\Exception $e) {
            // Handle errors during Facebook login validation
            return response()->json([
                'status' => 'error',
                'message' => 'Error logging in with Facebook: ' . $e->getMessage(),
            ], 400);
        }
    }

    // Handles the Facebook callback (used for web-based OAuth flows, if needed)
    public function handleCallback(Request $request)
    {
        $code = $request->input('code'); // Get the code from the URL query string

        try {
            // Get the access token using the code
            $accessToken = $this->facebook->getOAuth2Client()->getAccessTokenFromCode(
                $code,
                env('FACEBOOK_REDIRECT_URI')
            );

            // Use the access token to fetch user data
            $response = $this->facebook->get('/me?fields=id,name,email', $accessToken);
            $facebookUser = $response->getGraphUser(); // Get user data

            // Check if the user exists in the database, or create a new user
            $user = User::updateOrCreate(
                ['facebook_id' => $facebookUser['id']],
                [
                    'name' => $facebookUser['name'],
                    'email' => $facebookUser['email'],
                    'facebook_token' => $accessToken,
                ]
            );

            // Log the user in and return the token
            auth()->login($user);
            $token = auth()->user()->createToken('YourAppName')->plainTextToken;

            // Return a success response
            return response()->json([
                'status' => 'success',
                'message' => 'User logged in successfully via callback',
                'user' => $user,
                'token' => $token,
            ], 200);

        } catch (\Exception $e) {
            // Handle errors during the OAuth flow
            return response()->json([
                'status' => 'error',
                'message' => 'Error during callback: ' . $e->getMessage(),
            ], 400);
        }
    }
}
