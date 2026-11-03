<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\InstitutionController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemUnitController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\EnsureTenantAccess;
use App\Models\Category;
use App\Models\Department;
use App\Models\Institution;
use App\Models\Room;
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
    Route::resource('users', UserController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->middleware(EnsureTenantAccess::class);
    Route::resource('departments', DepartmentController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->middleware('can:create,'.Department::class);
    Route::resource('categories', CategoryController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->middleware('can:create,'.Category::class);
    Route::resource('items', ItemController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->middleware(EnsureTenantAccess::class);
    Route::resource('item-units', ItemUnitController::class)
        ->only(['index', 'store', 'update', 'destroy'])->middleware(EnsureTenantAccess::class);
    Route::get('item-units/{item_unit}/photo', [ItemUnitController::class, 'photo'])
        ->middleware(EnsureTenantAccess::class)->name('item-units.photo');
    Route::get('rooms', [RoomController::class, 'index'])->middleware(EnsureTenantAccess::class)->name('rooms.index');
    Route::resource('rooms', RoomController::class)->only(['store', 'update', 'destroy'])
        ->middleware('can:create,'.Room::class);
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
});
