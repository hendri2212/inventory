<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GoodController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TransactionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// HALAMAN
Route::get('/', function () {
    // return view('welcome');
    return view('/beranda');
});

// Route::get('/login', function () {
//     return view('Login/login');
// });

Route::get('/beranda', function () {
    return view('Beranda');
});

// GOODS


// SUPPLY


// ROOMS


// TRANSAKSI
Route::get('/index2', function () {
    return view('index2');
});

Route::get('/index', function () {
    return view('index');
});



Route::prefix('borrow')->group(function () {
    Route::get('/add', [BorrowController::class, 'create']);
        Route::get('/edit', [BorrowController::class, 'edit']);
});
Route::resource('borrow', BorrowController::class);


Route::prefix('category')->group(function () {
    Route::get('/add', [CategoryController::class, 'create']);
        Route::get('/edit', [CategoryController::class, 'edit']);
});
Route::resource('category', CategoryController::class);


Route::prefix('company')->group(function () {
    Route::get('/add', [CompanyController::class, 'create']);
        Route::get('/edit', [CompanyController::class, 'edit']);
});
Route::resource('company', CompanyController::class);

Route::prefix('condition')->group(function () {
    Route::get('/add', [ConditionController::class, 'create']);
        Route::get('/edit', [ConditionController::class, 'edit']);
});
Route::resource('condition', ConditionController::class);


Route::prefix('good')->group(function () {
    Route::get('/add', [GoodController::class, 'create']);
        Route::get('/edit', [GoodController::class, 'edit']);
});
Route::resource('good', GoodController::class);

Route::prefix('role')->group(function () {
    Route::get('/add', [RoleController::class, 'create']);
        Route::get('/edit', [RoleController::class, 'edit']);
});
Route::resource('role', RoleController::class);


Route::prefix('room')->group(function () {
    Route::get('/add', [RoomController::class, 'create']);
        Route::get('/edit', [RoomController::class, 'edit']);
});
Route::resource('room', RoomController::class);


Route::prefix('supplier')->group(function () {
    Route::get('/add', [SupplierController::class, 'create']);
        Route::get('/edit', [SupplierController::class, 'edit']);
});
Route::resource('supplier', SupplierController::class);


Route::prefix('transaction')->group(function () {
    Route::get('/add', [TransactionController::class, 'create']);
        Route::get('/edit', [TransactionController::class, 'edit']);
});
Route::resource('transaction', TransactionController::class);


Route::prefix('user')->group(function () {
    Route::get('/add', [UserController::class, 'create']);
        Route::get('/edit', [UserController::class, 'edit']);
});
Route::resource('user', UserController::class);

route::get('/login', [LoginController::class, 'index']);
route::post('/login', [LoginController::class, 'authenticate']);