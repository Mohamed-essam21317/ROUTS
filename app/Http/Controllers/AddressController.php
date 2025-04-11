<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Address;
use App\Models\User;
use App\Services\FirebaseService;
use App\Notifications\AddressUpdatedNotification;

class AddressController extends Controller
{
    protected $firebaseService;
    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    
     
        if (method_exists($this, 'middleware')) {
            $this->middleware('auth:api', ['except' => ['index', 'store', 'update', 'destroy']]);
        }
    }
    
   
    public function index()
    {
        $addresses = Address::all();

        return response()->json([
            'message' => 'Addresses retrieved successfully',
            'data' => $addresses
        ], 200);
    }

  
    public function store(Request $request)
    {
        $request->validate([
            'address' => 'required|string|max:255',
            'governorate' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'landmark' => 'nullable|string|max:255',
            'is_default' => 'boolean',
        ]);

        $address = Address::create([
            'user_id' => $request->user_id ?? null,
            'address' => $request->address,
            'governorate' => $request->governorate,
            'city' => $request->city,
            'landmark' => $request->landmark,
            'is_default' => $request->is_default ?? false,
        ]);

        return response()->json([
            'message' => 'Address created successfully without authentication',
            'data' => $address
        ], 201);
    }

    
    public function update(Request $request, $id)
    {
        $address = Address::find($id);
    
        if (!$address) {
            return response()->json(['error' => 'Address not found'], 404);
        }
    

        if (Auth::check() && $address->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    
        $address->update($request->all());
    
        return response()->json([
            'message' => 'Address updated successfully',
            'data' => $address
        ]);
    }
    
    public function destroy($id)
    {
        
        $address = Address::find($id);
    
        if (!$address) {
            return response()->json(['error' => 'Address not found'], 404);
        }
    
   
        if (Auth::check() && $address->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    
       
        $address->delete();
    
        return response()->json(['message' => 'Address deleted successfully.'], 200);
    }
}    