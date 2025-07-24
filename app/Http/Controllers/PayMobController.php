<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PayMobController extends Controller
{
    /**
     * Initiate payment and attach user_id to merchant_order_id
     */
    public function pay(Request $request)
    {
        $amount_cents = $request->amount * 100;
        $user_id = $request->user_id;

        $billingData = [
            "apartment" => "NA",
            "email" => $request->email,
            "floor" => "NA",
            "first_name" => $request->first_name,
            "street" => "NA",
            "building" => "NA",
            "phone_number" => $request->phone,
            "shipping_method" => "PKG",
            "postal_code" => "NA",
            "city" => "Cairo",
            "country" => "EG",
            "last_name" => $request->last_name,
            "state" => "Cairo"
        ];

        // 1. Get Auth Token
        $authResponse = Http::post('https://accept.paymob.com/api/auth/tokens', [
            "api_key" => env('PAYMOB_API_KEY')
        ]);

        $authData = $authResponse->json();
        $token = $authData['token'] ?? null;

        if (!$authResponse->successful() || !$token) {
            return response()->json([
                "error" => "Auth failed",
                "details" => $authData
            ], 500);
        }

        // 2. Create Order with merchant_order_id = user_id
        $orderResponse = Http::post('https://accept.paymob.com/api/ecommerce/orders', [
            "auth_token" => $token,
            "delivery_needed" => false,
            "amount_cents" => $amount_cents,
            "currency" => "EGP",
            "merchant_order_id" => $user_id,
            "items" => []
        ]);

        $orderData = $orderResponse->json();
        $orderId = $orderData['id'] ?? null;

        if (!$orderResponse->successful() || !$orderId) {
            return response()->json([
                "error" => "Order creation failed",
                "details" => $orderData
            ], 500);
        }

        // 3. Generate Payment Key
        $paymentKeyResponse = Http::post('https://accept.paymob.com/api/acceptance/payment_keys', [
            "auth_token" => $token,
            "amount_cents" => $amount_cents,
            "expiration" => 3600,
            "order_id" => $orderId,
            "billing_data" => $billingData,
            "currency" => "EGP",
            "integration_id" => env('PAYMOB_INTEGRATION_ID'),
            "tokenization" => true
        ]);

        $paymentKeyData = $paymentKeyResponse->json();
        $paymentToken = $paymentKeyData['token'] ?? null;

        if (!$paymentKeyResponse->successful() || !$paymentToken) {
            return response()->json([
                "error" => "Payment key generation failed",
                "details" => $paymentKeyData
            ], 500);
        }

        // 4. Return iframe URL
        $iframeUrl = "https://accept.paymob.com/api/acceptance/iframes/" . env('PAYMOB_IFRAME_ID') . "?payment_token=" . $paymentToken;

        return response()->json([
            "iframe_url" => $iframeUrl
        ]);
    }

    /**
     * Handle PayMob Webhook (DEBUGGING MODE)
     */
    public function handleWebhook(Request $request)
    {
        $data = $request->all();
        $userId = $data['order']['merchant_order_id'] ?? null;
        $cardToken = $data['token'] ?? null;

        // ✅ Save the transaction
        \App\Models\Transaction::create([
            'user_id' => $userId,
            'order_id' => $data['order']['id'] ?? null,
            'transaction_id' => $data['id'] ?? null,
            'amount_cents' => $data['amount_cents'] ?? 0,
            'success' => $data['success'] ?? false,
            'currency' => $data['currency'] ?? 'EGP',
            'card_token' => $cardToken,
            'raw_data' => $data,
        ]);

        // ✅ Also update the user with this card token
        if ($userId && $cardToken) {
            \App\Models\User::where('id', $userId)->update([
                'card_token' => $cardToken
            ]);
        }

        if ($data['success'] ?? false) {
            return response()->json(['message' => 'Payment confirmed'], 200);
        }

        return response()->json(['message' => 'Payment not successful'], 400);
    }
    private function generatePaymentToken($user, $amount, $authToken)
    {
        $amount_cents = $amount * 100;

        // 1. Create PayMob Order
        $orderResponse = Http::post('https://accept.paymob.com/api/ecommerce/orders', [
            "auth_token" => $authToken,
            "delivery_needed" => false,
            "amount_cents" => $amount_cents,
            "currency" => "EGP",
            "merchant_order_id" => $user->id . '_' . now()->timestamp,
            "items" => []
        ]);

        $orderData = $orderResponse->json();
        $orderId = $orderData['id'] ?? null;

        if (!$orderResponse->successful() || !$orderId) {
            throw new \Exception('❌ Failed to create order. Response: ' . json_encode($orderData));
        }

        // 2. Generate Payment Key
        $paymentKeyResponse = Http::post('https://accept.paymob.com/api/acceptance/payment_keys', [
            "auth_token" => $authToken,
            "amount_cents" => $amount_cents,
            "expiration" => 3600,
            "order_id" => $orderId,
            "billing_data" => [
                "apartment" => "NA",
                "email" => $user->email ?? "user@example.com",
                "floor" => "NA",
                "first_name" => $user->name ?? "User",
                "street" => "NA",
                "building" => "NA",
                "phone_number" => $user->phone ?? "01000000000",
                "shipping_method" => "PKG",
                "postal_code" => "NA",
                "city" => "Cairo",
                "country" => "EG",
                "last_name" => "Customer",
                "state" => "Cairo"
            ],
            "currency" => "EGP",
            "integration_id" => env('PAYMOB_INTEGRATION_ID')
        ]);

        $paymentKeyData = $paymentKeyResponse->json();
        $paymentToken = $paymentKeyData['token'] ?? null;

        if (!$paymentKeyResponse->successful() || !$paymentToken) {
            throw new \Exception('❌ Failed to generate payment key. Response: ' . json_encode($paymentKeyData));
        }

        return $paymentToken;
    }



    public function chargeSavedCard(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1'
        ]);

        $user = \App\Models\User::find($request->user_id);

        if (!$user->card_token) {
            return response()->json(['error' => 'No saved card token for this user'], 400);
        }

        // 1. Get Auth Token
        $auth = Http::post('https://accept.paymob.com/api/auth/tokens', [
            'api_key' => env('PAYMOB_API_KEY')
        ]);

        $token = $auth['token'] ?? null;

        if (!$auth->successful() || !$token) {
            return response()->json(['error' => 'PayMob auth failed'], 500);
        }

        // 2. Make the direct charge request
        $paymentResponse = Http::post('https://accept.paymob.com/api/acceptance/payments/pay', [
            "source" => [
                "identifier" => $user->card_token,
                "subtype" => "TOKEN"
            ],
            "payment_token" => $this->generatePaymentToken($user, $request->amount, $token)
        ]);

        return response()->json($paymentResponse->json(), $paymentResponse->status());
    }
}
