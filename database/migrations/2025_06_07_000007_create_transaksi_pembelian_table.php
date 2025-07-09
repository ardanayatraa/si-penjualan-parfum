<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaksiPembelianTable extends Migration
{
    public function up()
    {
        Schema::create('transaksi_pembelian', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_barang');
            $table->unsignedBigInteger('id_supplier');
            $table->dateTime('tanggal_transaksi');
            $table->integer('jumlah_pembelian');
            $table->decimal('harga', 15, 2); // Harga per unit
            $table->decimal('total', 15, 2); // Total = harga × jumlah
            $table->string('metode_pembayaran', 20)->default('cash');
            $table->string('status', 20)->default('pending');
            $table->timestamps();

            // Indexes untuk performa
            $table->index('tanggal_transaksi');
            $table->index('status');
            $table->index(['id_supplier', 'tanggal_transaksi']);


        });
    }

    public function down()
    {
        Schema::dropIfExists('transaksi_pembelian');
    }
}
