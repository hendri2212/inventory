<?php

use Illuminate\Support\Facades\Route;

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
    return view('Halaman_Utama/Main');
});

Route::get('/login', function () {
    return view('Login/login');
});

Route::get('/Dash', function () {
    return view('Halaman_Utama/dashboard');
});

Route::get('/barang', function () {
    return view('Halaman_Utama/Menu/barang');
});

Route::get('/supply', function () {
    return view('Halaman_Utama/Menu/Supply');
});

Route::get('/rooms', function () {
    return view('Halaman_Utama/Menu/Rooms');
});

Route::get('/transaksi', function () {
    return view('Halaman_Utama/Menu/Transaksi');
});



