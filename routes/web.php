<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';


Route::post('admins', [AdminController::class, 'store']);

// Get a specific Supervisor by ID
Route::get('admins/{id}', [AdminController::class, 'show']);

// Update a Supervisor by ID
Route::put('admins/{id}', [AdminController::class, 'update']);

// Delete a Supervisor by ID
Route::delete('admins/{id}', [AdminController::class, 'destroy']);


// Route to redirect the user to Facebook login page
Route::get('login/facebook', [LoginController::class, 'redirectToFacebook']);

// Callback route that Facebook will redirect to
Route::get('login/facebook/callback', [LoginController::class, 'handleFacebookCallback']);
