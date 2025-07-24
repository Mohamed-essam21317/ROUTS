<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function userTransactions($userId)
    {
        $user = \App\Models\User::find($userId);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $transactions = $user->transactions()->latest()->get();

        return response()->json([
            'user_id' => $user->id,
            'total_transactions' => $transactions->count(),
            'transactions' => $transactions
        ]);
    }


    public function allTransactions()
    {
        $transactions = \App\Models\Transaction::with('user')->latest()->get();

        return response()->json([
            'transactions' => $transactions
        ]);
    }
}
