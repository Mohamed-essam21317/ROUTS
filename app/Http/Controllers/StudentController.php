<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    // List all students
    public function index()
    {
        $students = Student::all();
        return response()->json($students);
    }

    // Show a single student
    public function show($id)
    {
        $student = Student::findOrFail($id);
        return response()->json($student);
    }

    // Create a new student
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|string|unique:students',
            'name' => 'required|string',
            'age' => 'required|integer',
            'parent_id' => 'required|exists:clients,id',
        ]);

        $student = Student::create($validated);
        return response()->json(['message' => 'Student created successfully.', 'student' => $student], 201);
    }

    // Update an existing student
    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string',
            'age' => 'sometimes|integer',
        ]);

        $student->update($validated);
        return response()->json(['message' => 'Student updated successfully.', 'student' => $student]);
    }

    // Delete a student
    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $student->delete();
        return response()->json(['message' => 'Student deleted successfully.']);
    }

    // Check if a student is inside their geofence
    public function checkGeofence(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');

        if ($student->isInsideGeofence($latitude, $longitude)) {
            return response()->json(['message' => 'The student is inside the geofence.']);
        } else {
            return response()->json(['message' => 'The student is outside the geofence.']);
        }
    }
}
