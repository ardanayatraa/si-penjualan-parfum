<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaksiPenjualanTable extends Migration
{
    public function up()
    {
        Schema::create('transaksi_penjualan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_kasir');
            $table->unsignedBigInteger('id_barang');
            $table->unsignedBigInteger('id_pajak');
            $table->dateTime('tanggal_transaksi');
            $table->integer('jumlah_terjual'); // Sesuai skema database
            $table->decimal('subtotal', 15, 2);
            $table->decimal('harga_pokok', 15, 2);
            $table->decimal('laba_bruto', 15, 2); // Field yang kurang
            $table->decimal('total_harga', 15, 2);
            $table->string('metode_pembayaran', 20); // Field yang kurang
            $table->string('status', 20)->default('pending'); // Field yang kurang
            $table->timestamps();

            // Indexes untuk performa
            $table->index('tanggal_transaksi');
            $table->index('status');
            $table->index(['id_kasir', 'tanggal_transaksi']);


        });
    }

    public function down()
    {
        Schema::dropIfExists('transaksi_penjualan');
    }
}
