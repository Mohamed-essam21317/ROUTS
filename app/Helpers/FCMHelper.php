<?php

namespace App\Helpers;

use Google\Auth\Credentials\ServiceAccountCredentials;

class FCMHelper
{
    public static function getAccessToken()
    {
        $credentialsPath = storage_path('app/firebase/ServiceAccountKey.json');
        $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];

        $credentials = new ServiceAccountCredentials($scopes, $credentialsPath);
        $token = $credentials->fetchAuthToken();

        return $token['access_token'] ?? null;
    }
}
