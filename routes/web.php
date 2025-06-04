<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\LaporanController;
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
    return view('auth.login');
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

    // routes/web.php

    Route::get('/pengaturan', function () {
        return view('page.pengaturan');
    })->name('pengaturan');


    Route::get('/supplier', function () {
        return view('page.supplier');
    })->name('supplier');

    Route::get('/transaksi-pembelian', function () {
        return view('page.transaksi-pembelian');
    })->name('transaksi-pembelian');

    Route::get('/transaksi-penjualan', function () {
        return view('page.transaksi-penjualan');
    })->name('transaksi-penjualan');

    Route::get('/return-barang', function () {
    return view('page.return');
})->name('return-barang');


    Route::get('/pajak-transaksi', function () {
        return view('page.pajak-transaksi');
    })->name('pajak-transaksi');
    Route::get('/user', function () {
        return view('page.user');
    })->name('user');
    Route::get('/laporan', function () {
        return view('page.laporan');
    })->name('laporan');

    Route::get('/laporan/cetak', [LaporanController::class, 'print'])->name('laporan.print');

    Route::get('/grafik-penjualan', [\App\Http\Controllers\GrafikPenjualanController::class, 'index'])->name('grafik.penjualan');
    Route::get('/grafik-penjualan/profit', [\App\Http\Controllers\GrafikPenjualanController::class, 'grafikProfit']);
    Route::get('/grafik-penjualan/produk-terlaris', [\App\Http\Controllers\GrafikPenjualanController::class, 'grafikProdukTerlaris']);

});

