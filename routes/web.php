<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InstitutionController;
use App\Http\Middleware\EnsureTenantAccess;
use App\Models\Institution;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::inertia('/login', 'Login')->name('login');
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::inertia('/', 'Welcome')->middleware(EnsureTenantAccess::class)->name('home');
    Route::resource('institutions', InstitutionController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->middleware('can:create,'.Institution::class);
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
});
