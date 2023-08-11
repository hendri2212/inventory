<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;

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
    return view('Main');
});

Route::get('/login', function () {
    return view('Login/login');
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

Route::get('/formr', function () {
    return view('Room/Add');
});

// TRANSAKSI
Route::get('/transaksi', function () {
    return view('Transaction/Data');
});

Route::get('/formtr', function () {
    return view('Transaction/Add');
});


// ETC.
Route::resource('category', CategoryController::class);

Route::get('/category/add', function () {
    return view('Category/Add');
});