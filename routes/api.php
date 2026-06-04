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
use App\Http\Controllers\CartItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SettingController;






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
    Route::put('/profile/password', [UserController::class, 'changePassword']);
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



Route::get('/settings/currency', [SettingController::class, 'currency']);

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::post('/categories', [CategoryController::class, 'store']);
Route::put('/categories/{id}', [CategoryController::class, 'update']);
Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);


Route::get(
    '/products/top-selling',
    [ProductController::class, 'topSelling']
);

Route::apiResource('products', ProductController::class);

Route::apiResource('cities', CityController::class);

Route::apiResource('areas', AreaController::class);


Route::middleware('auth:sanctum')->group(function () {

    Route::apiResource(
        'cart-items',
        CartItemController::class
    );

});

Route::get('/cities/{id}/areas', [CityController::class, 'areas']);
Route::get('/cities/{city}/areas', [AreaController::class, 'byCity']);




Route::middleware('auth:sanctum')->group(function () {

    Route::get('/orders/my', [OrderController::class, 'myOrders']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
});

Route::middleware(['auth:sanctum', 'admin'])->group(function () {

    Route::put('/settings/currency', [SettingController::class, 'updateCurrency']);

    Route::get('/orders', [OrderController::class, 'index']);

    Route::put(
        '/orders/{id}/status',
        [OrderController::class, 'updateStatus']
    );

    Route::delete('/orders/{id}', [OrderController::class, 'destroy']);
});



Route::apiResource(
    'brands',
    BrandController::class
);




Route::get('/products/{id}/reviews', [ReviewController::class, 'index']);
Route::post('/reviews', [ReviewController::class, 'store'])->middleware('auth:sanctum');