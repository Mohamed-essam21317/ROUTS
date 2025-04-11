<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\FirebaseService;
use App\Notifications\AddressUpdatedNotification;

class AddressRequestController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

   
     public function index()
    {
       
        $addresses = Address::all();

        return response()->json([
            'message' => 'Addresses retrieved successfully',
            'data' => $addresses
        ], 200);
    }

    public function update(Request $request, Address $address)
    {
        $request->validate([
            'address' => 'required|string|max:255',
            'governorate' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'landmark' => 'nullable|string|max:255',
            'is_default' => 'boolean',
        ]);

        if ($address->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $address->fill($request->all());

        if ($address->isDirty()) {
            $address->save();
        } else {
            return response()->json(['message' => 'No changes detected.'], 200);
        }

        $admin = User::where('role', 'admin')->first();
        $supervisor = User::where('role', 'supervisor')->first();
        $parent = Auth::user();

        $notificationData = [
            'title' => 'Address Updated',
            'body' => 'The address has been updated successfully.',
            'address' => $address->address
        ];

        if ($admin && $admin->device_token) {
            $admin->notify(new AddressUpdatedNotification($address, "Address updated by a parent."));
            $this->firebaseService->sendNotification($admin->device_token, $notificationData['title'], $notificationData['body'], $notificationData);
        }

        if ($supervisor && $supervisor->device_token) {
            $supervisor->notify(new AddressUpdatedNotification($address, "Address modified, please review."));
            $this->firebaseService->sendNotification($supervisor->device_token, $notificationData['title'], $notificationData['body'], $notificationData);
        }

        if ($parent && $parent->device_token) {
            $parent->notify(new AddressUpdatedNotification($address, "Your address has been updated successfully."));
            $this->firebaseService->sendNotification($parent->device_token, $notificationData['title'], $notificationData['body'], $notificationData);
        }

        return response()->json([
            'message' => 'Address updated successfully and notifications sent.',
            'data' => $address
        ]);
    }

    
    public function destroy(Address $address)
    {
        $user = Auth::user();

        if ($user->role === 'parent' && $address->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $address->delete();

        $admin = User::where('role', 'admin')->first();
        $supervisor = User::where('role', 'supervisor')->first();

        $notificationData = [
            'title' => 'Address Deleted',
            'body' => 'A parent has deleted an address request.',
        ];

        if ($admin && $admin->device_token) {
            $this->firebaseService->sendNotification($admin->device_token, $notificationData['title'], $notificationData['body']);
        }

        if ($supervisor && $supervisor->device_token) {
            $this->firebaseService->sendNotification($supervisor->device_token, $notificationData['title'], $notificationData['body']);
        }

        return response()->json(['message' => 'Address deleted successfully.']);
    }
}



