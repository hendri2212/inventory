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

Route::get('/', function () {
    // return view('welcome');
    return view('dashboard');
});

Route::get('/antarmuka', function () {
    return view('antarmuka');
});

Route::get('/ini_barang', function () {
    return view('ini_barang');
});

Route::get('/home', function () {
    return view('home');
});

Route::get('/data_barang', function () {
    return view('data_barang');
});

Route::get('/login', function () {
    return view('login');
});

Route::resource('category', CategoryController::class);