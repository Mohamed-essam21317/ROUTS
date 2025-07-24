<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentHealthInfo;
use Illuminate\Support\Facades\Auth;

class StudentHealthInfoController extends Controller
{

    public function store(Request $request)
    {
    
        // Validate the incoming data
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'medical_conditions' => 'nullable|string',
            'allergies' => 'nullable|string',
            'medications' => 'nullable|string',
            'emergency_contact' => 'nullable|string',
        ]);

        // Create a new health info record
        $healthInfo = StudentHealthInfo::create($validated);

        // Use makeHidden() to exclude any relationships or attributes that might cause recursion
        $healthInfo->makeHidden(['student']); // Replace 'student' with any problematic relationship or attribute

        // Return the response with limited fields
        return response()->json([
            'message' => 'Health information stored successfully',
            'data' => $healthInfo->only(['id', 'student_id', 'medical_conditions', 'allergies', 'medications', 'emergency_contact']),
        ], 201);
    }
    public function update(Request $request, $id)
    {
        // Check if the user is authenticated and is a parent
        // $user = Auth::user();
        // if ($user->role !== 'parent') {
        //     return response()->json(['message' => 'Unauthorized'], 403);
        // }

        // Find the existing health info record
        $healthInfo = StudentHealthInfo::find($id);
        if (!$healthInfo) {
            return response()->json(['message' => 'Health information not found'], 404);
        }

        // Validate the incoming data
        $validated = $request->validate([
            'medical_conditions' => 'nullable|string',
            'allergies' => 'nullable|string',
            'medications' => 'nullable|string',
            'emergency_contact' => 'nullable|string',
        ]);

        // Update the health info record
        $healthInfo->update($validated);

        return response()->json([
            'message' => 'Health information updated successfully',
            'data' => $healthInfo->only(['id', 'student_id', 'medical_conditions', 'allergies', 'medications', 'emergency_contact']),
        ], 200);
    }

    public function destroy($id)
    {
        // Check if the user is authenticated and is a parent
        // $user = Auth::user();
        // if ($user->role !== 'parent') {
        //     return response()->json(['message' => 'Unauthorized'], 403);
        // }

        // Find the existing health info record
        $healthInfo = StudentHealthInfo::find($id);
        if (!$healthInfo) {
            return response()->json(['message' => 'Health information not found'], 404);
        }

        // Delete the health info record
        $healthInfo->delete();

        return response()->json(['message' => 'Health information deleted successfully'], 200);
    }

    public function show($student_id)
    {
        // Check if the user is authenticated and is a parent
        // $user = Auth::user();
        // if ($user->role !== 'parent') {
        //     return response()->json(['message' => 'Unauthorized'], 403);
        // }

        // Retrieve the health info for the given student
        $healthInfo = StudentHealthInfo::where('student_id', $student_id)->first();
        if (!$healthInfo) {
            return response()->json(['message' => 'Health information not found for this student'], 404);
        }

        return response()->json([
            'data' => $healthInfo->only(['id', 'student_id', 'medical_conditions', 'allergies', 'medications', 'emergency_contact']),
        ], 200);
    }
}
