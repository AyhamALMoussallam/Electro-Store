<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleAuthController;

Route::get('/', function () {
    return view('profile');
});

Route::get('login/', function () {
    return view('auth.login');
});
Route::get('signup/', function () {
    return view('auth.signup');
});
Route::get('forgot-password/', function () {
    return view('auth.forgot-password');
});
Route::get('reset-password/', function () {
    return view('auth.reset-password');
});
Route::get('home/', function () {
    return view('index');
});
Route::get('product/', function () {
    return view('product');
});
Route::get('store/', function () {
    return view('store');
});
Route::get('checkout/', function () {
    return view('checkout');
});
Route::get('dashboard/', function () {
    return view('dashboard');
});


Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);

Route::get('/auth/callback', function () {
    return view('auth.callback');
})->name('auth.callback');
