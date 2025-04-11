<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Exception\FirebaseException;

class FirebaseService
{
    protected $messaging;

    public function __construct()
    {
     
        $serviceAccountPath = storage_path('app/firebase/serviceAccount.json');

        $this->messaging = (new Factory)
    ->withServiceAccount(storage_path('app/firebase/serviceAccount.json'))
    ->createMessaging();

    }

    public function sendNotification($deviceToken, $title, $body, $data = [])
    {
        if (!$deviceToken) {
            return ['error' => 'Device token is required'];
        }

        try {
            $notification = FirebaseNotification::create()
                ->withTitle($title)
                ->withBody($body);

            $message = CloudMessage::withTarget('token', $deviceToken)
                ->withNotification($notification)
                ->withData($data);

            return $this->messaging->send($message);
        } catch (MessagingException | FirebaseException $e) {
            return ['error' => 'Failed to send notification', 'message' => $e->getMessage()];
        }
    }
}

