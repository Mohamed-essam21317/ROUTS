<?php

namespace App\Services;

use Google\Auth\OAuth2;
use Illuminate\Support\Facades\Http;

class FcmService
{
    private $credentials;
    private $projectId;

    public function __construct()
    {
        $this->credentials = json_decode(file_get_contents(storage_path('app/firebase/firebase_credentials.json')), true);
        $this->projectId = $this->credentials['project_id'];
    }

    public function sendNotification($token, $title, $body)
    {
        $accessToken = $this->getAccessToken();

        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

        $response = Http::withToken($accessToken)
            ->post($url, [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                ]
            ]);

        return $response->successful();
    }

    private function getAccessToken()
    {
        $oauth = new OAuth2([
            'audience' => 'https://oauth2.googleapis.com/token',
            'issuer' => $this->credentials['client_email'],
            'signingAlgorithm' => 'RS256',
            'signingKey' => $this->credentials['private_key'],
            'tokenCredentialUri' => 'https://oauth2.googleapis.com/token',
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
        ]);

        $authToken = $oauth->fetchAuthToken();
        return $authToken['access_token'];
    }
}
