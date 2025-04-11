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
use App\Http\Controllers\SMSController;
use App\Http\Controllers\StudentController;   
use App\Http\Controllers\ParentProfileController;// Parent Controller
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\AddressController; 
use App\Http\Controllers\AddressRequestController;
use App\Http\Controllers\FcmTokenController;
use App\Http\Controllers\Api\Supervisor\SchoolPlanController;





// Authentication Routes
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);


Route::post('logout', [AuthController::class, 'logout']);
// Parent Profile Routes
Route::get('/parent/profile', [ParentProfileController::class, 'show']);
    Route::put('/parent/profile', [ParentProfileController::class, 'update']);
    Route::delete('/parent/delete', [ParentProfileController::class, 'destroy']);

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

// Child Routes
Route::post('/children', [ChildController::class, 'store']);
Route::get('/children', [ChildController::class, 'index']);
//Route::get('/children/{id}', [ChildController::class, 'show']);


Route::post('/send-sms', [SMSController::class, 'sendSMS']);

// Student Details Route
Route::get('student-details', [StudentController::class, 'getStudentDetails']);

Route::get('/weather', [WeatherController::class,'show']);

// Address Routes
//Route::middleware('auth:sanctum')->group(function () {
    Route::get('/addresses', [AddressController::class, 'index']);
    Route::post('/addresses', [AddressController::class, 'store']);
    Route::put('/addresses/{address}', [AddressController::class, 'update']);
    Route::delete('/addresses/{address}', [AddressController::class, 'destroy']);

    Route::post('/address-requests', [AddressRequestController::class, 'store']); 
    Route::put('/address-requests/{addressRequest}', [AddressRequestController::class, 'updateStatus']); 
//}

//Fcm Token
Route::post('/save-fcm-token', [FcmTokenController::class, 'store']);

//supervisor planphp 


Route::get('/supervisor/plans', [SchoolPlanController::class, 'index']);
