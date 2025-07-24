<?php

// app/Http/Controllers/SupervisorController.php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Supervisor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // Store the supervisor data
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'school_id' => 'nullable|exists:schools,id', // Ensure school_id exists in the schools table
        ]);

        // Create a user account for the supervisor
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'admin',
            'phone' => $request->phone_number ?? '', // Default to empty string if phone is not provided
            'role_based_id' => 3,
            'school_id' => $request->school_id,
        ]);

        // Create the admin record and associate it with the user
        $admin = Admin::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => $user->password,
            'phone' => $request->phone_number ?? '', // Default to empty string if phone is not provided
            'role_based_id' => 3,
            'school_id' => $request->school_id,
        ]);

        return response()->json([
            'message' => 'Admin created successfully.',
            'user' => $user,
            'admin' => $admin,
        ], 201);
    }

    // Show a specific supervisor's details
    public function show($id)
    {
        $supervisor = Admin::find($id);

        if (!$supervisor) {
            return response()->json(['message' => 'Admin not found'], 404);
        }

        return response()->json($supervisor);
    }

    // Update the admin's data
    public function update(Request $request, $id)
    {
        $admin = Admin::find($id);

        if (!$admin) {
            return response()->json(['message' => 'Admin not found'], 404);
        }

        // Validate the incoming data
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:admins,email,' . $admin->id,
            'password' => 'sometimes|required|confirmed|min:8',
        ]);

        // Update the supervisor's details
        if ($request->has('name')) {
            $admin->name = $request->name;
        }
        if ($request->has('email')) {
            $admin->email = $request->email;
        }
        if ($request->has('password')) {
            $admin->password = Hash::make($request->password);
        }

        $admin->save();

        return response()->json([
            'status' => 'success',
            'message' => 'admin updated successfully!',
            'supervisor' => $admin
        ]);
    }

    // Delete the supervisor
    public function destroy($id)
    {
        $admin = Admin::find($id);

        if (!$admin) {
            return response()->json(['message' => 'admin not found'], 404);
        }

        // Debugging: Check if the supervisor has an associated user
        if (!$admin->user) {
            return response()->json(['message' => 'Associated user not found for this admin'], 404);
        }

        // Delete the associated user
        $admin->user->forceDelete();

        // Delete the supervisor
        $admin->delete();

        return response()->json(['message' => 'admin and associated user deleted successfully']);
    }
}
