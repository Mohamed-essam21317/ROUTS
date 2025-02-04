<?php
// App\Http\Controllers\StudentController.php
namespace App\Http\Controllers;
use App\Models\Student;
use App\Models\School;
use Illuminate\Http\Request;
class StudentController extends Controller
{
    public function getStudentDetails(Request $request)
    {
        // جلب school_name و student_id من الـ query parameters
        $schoolName = $request->input('school_name');
        $studentId = $request->input('student_id');

        // التحقق من وجود المدرسة بالـ school_name
        $school = School::where('school_name', $schoolName)->first();

        // التحقق من وجود المدرسة
        if (!$school) {
            return response()->json(['message' => 'School not found', 'school_name' => $schoolName], 404);
        }

        // جلب الطالب بناءً على الـ student_id و الـ school_id
        $student = Student::where('id', $studentId)->where('school_id', $school->id)->first();

        // التحقق من وجود الطالب
        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        // إرجاع البيانات
        return response()->json($student, 200);
    }
}
