<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::inertia('/login', 'Login')->name('login');
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::inertia('/', 'Welcome')->name('home');
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
});
