<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    // ✅ API لجلب سجل الإشعارات للطالب مع فلترة اختيارية
    public function getStudentNotifications(Request $request)
    {
        // جلب student_id من الـ request
        $student_id = $request->input('student_id');

        if (!$student_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'student_id is required'
            ], 400);
        }

        // التحقق من وجود الطالب
        $student = DB::table('students')->where('id', (int)$student_id)->first();

        if (!$student) {
            return response()->json([
                'status' => 'error',
                'message' => 'Student not found'
            ], 404);
        }

        // بناء الاستعلام لجلب الإشعارات
        $query = DB::table('notifications')
            ->join('students', 'notifications.student_id', '=', 'students.id')
            ->select(
                'notifications.id',
                'notifications.title',
                'notifications.body',
                'notifications.model_type',
                'notifications.read_at',
                'notifications.date',
                'notifications.time',
                'notifications.student_id',
                'students.first_name',
                'students.last_name'
            )
            ->where('notifications.student_id', (int)$student_id);

        // تطبيق الفلاتر لو موجودة
        if ($request->has('model_type')) {
            $query->where('notifications.model_type', $request->model_type);
        }

        if ($request->has('date')) {
            $query->whereDate('notifications.date', $request->date);
        }

        $notifications = $query->orderBy('notifications.created_at', 'desc')->get();

        // جلب النموذج والرسائل من قاعدة البيانات
        $templates = DB::table('notification_templates')->get();
        $model_types = [];
        $messages = [];

        foreach ($templates as $template) {
            $model_types[$template->model_type] = $template->title;
            $messages[$template->model_type] = [
                'title' => $template->title,
                'body' => $template->body
            ];
        }

        return response()->json([
            'status' => 'success',
            'student' => [
                'student_id' => $student->id,
                'student_name' => $student->first_name . ' ' . $student->last_name
            ],
            'notifications' => $notifications,
            'model_types' => $model_types,
            'messages' => $messages
        ]);
    }

    // ✅ API لجلب سجل الإشعارات بدون فلترة (History)
    public function history(Request $request)
    {
        $student_id = $request->input('student_id');

        if (!$student_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'student_id is required'
            ], 400);
        }

        $student = DB::table('students')->where('id', (int)$student_id)->first();

        if (!$student) {
            return response()->json([
                'status' => 'error',
                'message' => 'Student not found'
            ], 404);
        }

        $notifications = DB::table('notifications')
            ->where('student_id', (int)$student_id)
            ->orderBy('created_at', 'desc')
            ->get();

        $templates = DB::table('notification_templates')->get();
        $model_types = [];
        $messages = [];

        foreach ($templates as $template) {
            $model_types[$template->model_type] = $template->title;
            $messages[$template->model_type] = [
                'title' => $template->title,
                'body' => $template->body
            ];
        }

        return response()->json([
            'status' => 'success',
            'student' => [
                'student_id' => $student->id,
                'student_name' => $student->first_name . ' ' . $student->last_name
            ],
            'notifications' => $notifications,
            'model_types' => $model_types,
            'messages' => $messages
        ]);
    }

    // ✅ API لإضافة إشعار جغرافي جديد للطالب
    public function addGeofencingNotification(Request $request)
    {
        $request->validate([
            'student_id' => 'required|integer',
            'title'      => 'required|string',
            'body'       => 'required|string',
        ]);

        // تحقق من وجود الطالب
        $student = DB::table('students')->where('id', (int)$request->student_id)->first();
        if (!$student) {
            return response()->json([
                'status' => 'error',
                'message' => 'Student not found'
            ], 404);
        }

        // إضافة الإشعار في جدول notifications
        DB::table('notifications')->insert([
            'title'        => $request->title,
            'body'         => $request->body,
            'model_type'   => 'Geofencing',
            'read_at'      => null,
            'date'         => now()->toDateString(),
            'time'         => now()->toTimeString(),
            'student_id'   => $request->student_id,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Geofencing notification added successfully.'
        ]);
    }
}
