<?php

use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\OTPController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\BusController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\GeofencingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\FacebookAuthController;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use App\Http\Controllers\NotificationController;
// Students Routes
Route::get('students', [StudentController::class, 'index']); // List all students
Route::get('students/{id}', [StudentController::class, 'show']); // Get a specific student
Route::post('students', [StudentController::class, 'store']); // Create a new student
Route::put('students/{id}', [StudentController::class, 'update']); // Update a student
Route::delete('students/{id}', [StudentController::class, 'destroy']); // Delete a student
Route::post('students/{id}/check-geofence', [StudentController::class, 'checkGeofence']); // Check if a student is in the geofence

// Clients Routes (Parents, Drivers, Supervisors, Admins)
Route::get('clients', [ClientController::class, 'index']); // List all clients
Route::get('clients/{id}', [ClientController::class, 'show']); // Get a specific client
Route::post('clients', [ClientController::class, 'store']); // Create a new client
Route::put('clients/{id}', [ClientController::class, 'update']); // Update a client
Route::delete('clients/{id}', [ClientController::class, 'destroy']); // Delete a client

// Buses Routes
Route::get('buses', [BusController::class, 'index']); // List all buses
Route::get('buses/{id}', [BusController::class, 'show']); // Get a specific bus
Route::post('buses', [BusController::class, 'store']); // Create a new bus
Route::put('buses/{id}', [BusController::class, 'update']); // Update a bus
Route::delete('buses/{id}', [BusController::class, 'destroy']); // Delete a bus
Route::put('buses/{id}/location', [BusController::class, 'updateLocation']); // Update a bus's location

// Routes for Bus Routes
Route::get('routes', [RouteController::class, 'index']); // List all routes
Route::get('routes/{id}', [RouteController::class, 'show']); // Get a specific route
Route::post('routes', [RouteController::class, 'store']); // Create a new route
Route::put('routes/{id}', [RouteController::class, 'update']); // Update a route
Route::delete('routes/{id}', [RouteController::class, 'destroy']); // Delete a route

// Geofencing Routes
Route::post('geofencing/student/{id}/check', [GeofencingController::class, 'checkStudentGeofence']); // Check student geofence
Route::post('geofencing/bus/{id}/check', [GeofencingController::class, 'checkBusGeofence']); // Check bus geofence
 // facebook routes
Route::post('/auth/facebook-login', [SocialAuthController::class, 'facebookLogin']);
Route::get('/auth/facebook-callback', [SocialAuthController::class, 'handleCallback']);
Route::post('/auth/facebook-login', [FacebookAuthController::class, 'facebookLogin']);

// login
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//google Routes
Route::post('/google-login', [GoogleAuthController::class, 'googleLogin']);


//set password


Route::post('/send-otp', [AuthController::class, 'sendOtp']);
Route::post('/verify-otp-set-password', [AuthController::class, 'verifyOtpAndSetPassword']);



Route::post('/send-emergency-notification', [NotificationController::class, 'sendEmergencyNotification']);



Route::post('/send-otp', [OTPController::class, 'sendOTP']);
