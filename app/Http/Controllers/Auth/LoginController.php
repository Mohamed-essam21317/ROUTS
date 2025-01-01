<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{

    public function redirectToFacebook(): Response
    {
        return Socialite::driver('facebook')->redirect();
    }

    /**
     * Obtain the user information from Facebook.
     *
     * @return \Illuminate\Http\RedirectResponse|Response
     */
    public function handleFacebookCallback(): Response|\Illuminate\Http\RedirectResponse
    {
        // Get the user data from Facebook
        $facebookUser = Socialite::driver('facebook')->user();

        // Check if the user already exists in the database using the Facebook ID
        $user = User::where('facebook_id', $facebookUser->getId())->first();

        if (!$user) {
            // If the user does not exist, create a new user
            $user = User::create([
                'name' => $facebookUser->getName(),
                'email' => $facebookUser->getEmail(),
                'facebook_id' => $facebookUser->getId(),
                'avatar' => $facebookUser->getAvatar(),
            ]);
        }

        // Log the user in
        Auth::login($user);

        // Redirect to the intended page (or home page)
        return redirect()->intended('/');
    }
}

