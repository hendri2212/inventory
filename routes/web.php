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
    return view('Menu/Main');
});

Route::get('/login', function () {
    return view('Login/login');
});

Route::get('/Dash', function () {
    return view('Halaman_Utama/dashboard');
});

// BARANG
Route::get('/barang', function () {
    return view('Menu/Barang/barang');
});

Route::get('/formbrg', function () {
    return view('Menu/Barang/Form');
});

// SUPPLY
Route::get('/supply', function () {
    return view('Menu/Supply/Supply');
});

Route::get('/formspl', function () {
    return view('Menu/Supply/Form');
});

// ROOMS
Route::get('/rooms', function () {
    return view('Menu/Rooms/Rooms');
});

Route::get('/formr', function () {
    return view('Menu/Rooms/Form');
});

// TRANSAKSI
Route::get('/transaksi', function () {
    return view('Menu/Transaksi/Transaksi');
});

Route::get('/formtr', function () {
    return view('Menu/Transaksi/Form');
});


// ETC.
Route::resource('Category', CategoryController::class);

Route::get('/add', function () {
    return view('Category/add');
});