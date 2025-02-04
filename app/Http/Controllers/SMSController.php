<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TwilioService;

class SMSController extends Controller
{
    public function __construct(protected TwilioService $twilio) {}

    public function sendSMS(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string|max:160',
        ]);

        $this->twilio->sendSMS($validated['phone'], $validated['message']);

        return response()->json(['message' => 'SMS sent successfully!']);
    }
}
