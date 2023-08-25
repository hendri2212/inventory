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
    return view('/index');
});

Route::get('/login', function () {
    return view('Login/login');
});

Route::get('/beranda', function () {
    return view('Beranda');
});

// GOODS
Route::get('/barang', function () {
    return view('Goods/Data');
});

Route::get('/formbrg', function () {
    return view('Goods/Add');
});

// SUPPLY
Route::get('/supply', function () {
    return view('Supplier/Data');
});

Route::get('/formspl', function () {
    return view('Supplier/Add');
});

// ROOMS
Route::get('/rooms', function () {
    return view('Room/Data');
});

Route::get('/index3', function () {
    return view('index3');
});

// TRANSAKSI
Route::get('/index2', function () {
    return view('index2');
});

Route::get('/index', function () {
    return view('index');
});


// INI APA
Route::prefix('category')->group(function () {
    Route::get('/add', function () {
        return view('Category/Add');
    });
});
Route::resource('category', CategoryController::class);


Route::prefix('company')->group(function () {
    Route::get('/add', function () {
        return view('Company/Add');
    });
});
Route::resource('company', CompanyController::class);


Route::prefix('good')->group(function () {
    Route::get('/add', function () {
        return view('Good/Add');
    });
});
Route::resource('good', GoodController::class);


Route::prefix('room')->group(function () {
    Route::get('/add', function () {
        return view('Room/Add');
    });
});
Route::resource('room', RoomController::class);


Route::prefix('supplier')->group(function () {
    Route::get('/add', function () {
        return view('Supplier/Add');
    });
});
Route::resource('supplier', SupplierController::class);


Route::prefix('transaction')->group(function () {
    Route::get('/add', function () {
        return view('Transaction/Add');
    });
});
Route::resource('transaction', TransactionController::class);


Route::prefix('user')->group(function () {
    Route::get('/add', function () {
        return view('User/Add');
    });
});
Route::resource('user', UserController::class);