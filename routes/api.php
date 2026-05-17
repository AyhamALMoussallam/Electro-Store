<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Auth\Events\Verified;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\AreaController;




// ------------------------------
// Public Routes
// ------------------------------

// Sign Up
Route::post('/signup', [UserController::class, 'signup']);

// Login
Route::post('/login', [UserController::class, 'login']);





Route::middleware('auth:sanctum')->group(function () {

    // -------- User Routes --------
    Route::get('/user', [UserController::class, 'getCurrentUser']);
    Route::post('/logout', [UserController::class, 'logout']);
    Route::put('/profile', [UserController::class, 'updateProfile']);
    Route::delete('/profile', [UserController::class, 'deleteAccount']);
});


// -------- Password Reset (no auth required) --------
Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword']);
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);


// Verify email

Route::get('/email/verify/{id}/{hash}',
    [EmailVerificationController::class, 'verify']
)->middleware('signed')->name('verification.verify');

Route::post('/email/verification-notification',
    [EmailVerificationController::class, 'resend']
)->middleware('throttle:6,1');



Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::post('/categories', [CategoryController::class, 'store']);
Route::put('/categories/{id}', [CategoryController::class, 'update']);
Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);


Route::apiResource('products', ProductController::class);

Route::apiResource('cities', CityController::class);

Route::apiResource('areas', AreaController::class);