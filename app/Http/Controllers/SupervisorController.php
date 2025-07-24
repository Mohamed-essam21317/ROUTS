<?php

// app/Http/Controllers/SupervisorController.php

namespace App\Http\Controllers;

use App\Models\Supervisor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SupervisorController extends Controller
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
            'phone' => $request->phone_number ?? '',
            'role' => 'supervisor', // Assign the role of supervisor
            'role_based_id' => 2,  // Set role_based_id to 2
            'school_id' => $request->school_id,  // Include school_id
        ]);

        // Create the supervisor record and associate it with the user
        $supervisor = Supervisor::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone_number ?? '', // Default to empty string if phone is not provided
            'password' => $user->password,
            'role_based_id' => 2,  // Set role_based_id to 2
            'school_id' => $request->school_id,  // Include school_id
        ]);

        return response()->json([
            'message' => 'Supervisor created successfully.',
            'user' => $user,
            'supervisor' => $supervisor,
        ], 201);
    }

    // Show a specific supervisor's details
    public function show($id)
    {
        $supervisor = Supervisor::find($id);

        if (!$supervisor) {
            return response()->json(['message' => 'Supervisor not found'], 404);
        }

        return response()->json($supervisor);
    }

    // Update the supervisor's data
    public function update(Request $request, $id)
    {
        $supervisor = Supervisor::find($id);

        if (!$supervisor) {
            return response()->json(['message' => 'Supervisor not found'], 404);
        }

        // Validate the incoming data
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:supervisors,email,' . $supervisor->id,
            'password' => 'sometimes|required|confirmed|min:8',
        ]);

        // Update the supervisor's details
        if ($request->has('name')) {
            $supervisor->name = $request->name;
        }
        if ($request->has('email')) {
            $supervisor->email = $request->email;
        }
        if ($request->has('password')) {
            $supervisor->password = Hash::make($request->password);
        }

        $supervisor->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Supervisor updated successfully!',
            'supervisor' => $supervisor
        ]);
    }

    // Delete the supervisor
    public function destroy($id)
    {
        $supervisor = Supervisor::find($id);

        if (!$supervisor) {
            return response()->json(['message' => 'Supervisor not found'], 404);
        }

        // Debugging: Check if the supervisor has an associated user
        if (!$supervisor->user) {
            return response()->json(['message' => 'Associated user not found for this supervisor'], 404);
        }

        // Delete the associated user
        $supervisor->user->forceDelete();

        // Delete the supervisor
        $supervisor->delete();

        return response()->json(['message' => 'Supervisor and associated user deleted successfully']);
    }
}
