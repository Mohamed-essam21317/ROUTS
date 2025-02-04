<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\BusController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\GeofencingController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Api\ChildController;
use App\Http\Controllers\SMSController;// Authentication Routes
use App\Http\Controllers\StudentController;
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);

// Routes requiring authentication
Route::middleware('auth:sanctum')->group(function () {
Route::get('/user', [AuthController::class, 'getUser']);
Route::post('logout', [AuthController::class, 'logout']);
});

// Students Routes
Route::apiResource('students', StudentController::class);
Route::post('students/{id}/check-geofence', [StudentController::class, 'checkGeofence']);

// Clients Routes
Route::apiResource('clients', ClientController::class);

// Buses Routes
Route::apiResource('buses', BusController::class);
Route::post('/bus/location/update', [BusController::class, 'updateLocation']);

// Routes for Bus Routes
Route::apiResource('routes', RouteController::class);

// Geofencing Routes
Route::post('geofencing/student/{id}/check', [GeofencingController::class, 'checkStudentGeofence']);
Route::post('geofencing/bus/{id}/check', [GeofencingController::class, 'checkBusGeofence']);

// Social Login Routes
Route::post('/auth/facebook-login', [SocialAuthController::class, 'facebookLogin']);
Route::get('/auth/facebook-callback', [SocialAuthController::class, 'handleCallback']);
Route::post('/google-login', [GoogleAuthController::class, 'googleLogin']);

// Notification Routes
Route::post('/send-emergency-notification', [NotificationController::class, 'sendEmergencyNotification']);


Route::post('/children', [ChildController::class, 'store']);
Route::get('/children', [ChildController::class, 'index']);
//Route::get('/children/{id}', [ChildController::class, 'show']);


Route::post('/send-sms', [SMSController::class, 'sendSMS']);

Route::get('student-details', [StudentController::class, 'getStudentDetails']);
