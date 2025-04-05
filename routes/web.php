<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TransaksiPembelianController;
use App\Http\Controllers\TransaksiPenjualanController;
use App\Http\Controllers\PajakTransaksiController;
use App\Http\Controllers\UserController;
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
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/barang', function () {
        return view('page.barang');
    })->name('barang');

    Route::get('/supplier', function () {
        return view('page.supplier');
    })->name('supplier');

    Route::get('/transaksi-pembelian', function () {
        return view('page.transaksi-pembelian');
    })->name('transaksi-pembelian');

    Route::get('/transaksi-penjualan', function () {
        return view('page.transaksi-penjualan');
    })->name('transaksi-penjualan');

    Route::get('/pajak-transaksi', function () {
        return view('page.pajak-transaksi');
    })->name('pajak-transaksi');
    Route::get('/user', function () {
        return view('page.user');
    })->name('user');
    Route::get('/laporan', function () {
        return view('page.laporan');
    })->name('laporan');

});
