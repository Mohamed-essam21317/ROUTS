<?php

use Illuminate\Support\Facades\Route; // Ensure the Route facade is imported
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
use App\Http\Controllers\ParentProfileController; // Parent Controller
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\AddressRequestController;
use App\Http\Controllers\FcmTokenController;
use App\Http\Controllers\Api\Supervisor\SchoolPlanController;
use App\Http\Controllers\StudentHealthInfoController;
use App\Http\Controllers\EmergencyAlertController;
use App\Http\Controllers\FCMController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportIssueController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\BusLocationController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PaymobController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\StatusControlle;





// Authentication Routes
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);

Route::post('/geofence/create', [GeofencingController::class, 'create']);

Route::get('/profile', [ProfileController::class, 'index']);


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

Route::get('/weather', [WeatherController::class, 'show']);

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
Route::post('/fcm/store-token', [FCMController::class, 'storeToken']);
Route::post('/save-fcm-token', [FCMController::class, 'store']);

//supervisor planphp


Route::get('/supervisor/plans', [SchoolPlanController::class, 'index']);


// Route::middleware('auth:sanctum')->group(function () {
//     Route::post('/health-info', [StudentHealthInfoController::class, 'store']);
//     Route::put('/health-info/{id}', [StudentHealthInfoController::class, 'update']);
//     Route::delete('/health-info/{id}', [StudentHealthInfoController::class, 'destroy']);
//     Route::get('/health-info/{student_id}', [StudentHealthInfoController::class, 'show']);
// });


Route::post('/health-info', [StudentHealthInfoController::class, 'store']);
Route::put('/health-info/{id}', [StudentHealthInfoController::class, 'update']);
Route::delete('/health-info/{id}', [StudentHealthInfoController::class, 'destroy']);
Route::get('/health-info/{student_id}', [StudentHealthInfoController::class, 'show']);

Route::post('/geofence/create', [GeofencingController::class, 'create']);

// Route::post('/emergency-alert', [EmergencyAlertController::class, 'send']);
Route::middleware('auth:sanctum')->post('/emergency-alert', [EmergencyAlertController::class, 'send']);
Route::post('/test', [TestController::class, 'test']);
// Route::post('/emergency-alert', function() {
//     return response()->json(['status' => 'ok']);
// });

// report issue
Route::post('/report-issue', [ReportIssueController::class, 'report'])->name('report.issue');
// Route::post('supervisors', [SupervisorController::class, 'store'])->name('supervisors.store');

Route::post('supervisors', [SupervisorController::class, 'store']);

// Get a specific Supervisor by ID
Route::get('supervisors/{id}', [SupervisorController::class, 'show']);

// Update a Supervisor by ID
Route::put('supervisors/{id}', [SupervisorController::class, 'update']);

// Delete a Supervisor by ID
Route::delete('supervisors/{id}', [SupervisorController::class, 'destroy']);


Route::post('admins', [AdminController::class, 'store']);

// Get a specific Supervisor by ID
Route::get('admins/{id}', [AdminController::class, 'show']);

// Update a Supervisor by ID
Route::put('admins/{id}', [AdminController::class, 'update']);

// Delete a Supervisor by ID
Route::delete('admins/{id}', [AdminController::class, 'destroy']);



Route::apiResource('buses', BusController::class);
Route::post('/bus/location/update', [BusController::class, 'updateLocation']);
Route::put('buses/{bus}/location', [BusLocationController::class, 'updateLocation'])->name('buses.location.update');


Route::post('/update-bus-location', [BusLocationController::class, 'updateLocation']);
Route::get('/get-bus-location/{busId}', [BusLocationController::class, 'getBusLocation']);


Route::post('/geofence/create', [GeofencingController::class, 'create']);


Route::post('/bus/{busId}/check-proximity', [GeofencingController::class, 'checkProximity']);

// Route::post('/paymob/authenticate', [PaymobController::class, 'authenticate']);
// Route::post('/paymob/create-order', [PaymobController::class, 'createOrder']);
// Route::post('/paymob/generate-payment-key', [PaymobController::class, 'generatePaymentKey']);
// Route::post('/paymob/webhook', [PaymobController::class, 'webhook']);

Route::post('/pay', [PayMobController::class, 'pay']);
Route::post('/paymob/webhook', [PayMobController::class, 'handleWebhook']);
Route::post('/pay/charge-saved-card', [PayMobController::class, 'chargeSavedCard']);
Route::get('/users/{user}/transactions', [TransactionController::class, 'userTransactions']);

Route::get('/test-connection', function () {
    return response()->json(['status' => 'ok']);
});


Route::get('/buses/{id}/statuses', [\App\Http\Controllers\StatusController::class, 'getByBus']);


Route::get('/transactions', [TransactionController::class, 'allTransactions']);


Route::post('/notifications/geofencing', [NotificationController::class, 'addGeofencingNotification']);
Route::get('/notifications/student', [NotificationController::class, 'getStudentNotifications']);
Route::get('/notifications/history', [NotificationController::class, 'history']);
