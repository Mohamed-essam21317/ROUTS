<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Child;

class ChildController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'school_id' => 'required|exists:schools,id',
            'student_id' => 'required|exists:students,id',
        ]);

        // التحقق من أن الطالب غير مرتبط بطفل آخر
        $existingChild = Child::where('student_id', $request->student_id)->first();
        if ($existingChild) {
            return response()->json(['error' => 'This student is already assigned to a child.'], 400);
        }

        // إنشاء الطفل
        $child = Child::create([
            'name' => $request->name,
            'school_id' => $request->school_id,
            'student_id' => $request->student_id,
            'parent_id' => auth()->id(),
        ]);

        return response()->json($child, 201);
    }
    public function index()
    {
        // جلب الأطفال المرتبطين بولي الأمر الحالي
        $children = Child::with('school') // جلب اسم المدرسة
        ->where('parent_id', auth()->id())
            ->get();

        return response()->json($children);
    }

}
