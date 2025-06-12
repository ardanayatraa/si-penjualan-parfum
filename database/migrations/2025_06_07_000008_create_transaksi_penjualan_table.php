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
            $table->decimal('subtotal', 15, 2);
            $table->decimal('harga_pokok', 15, 2);
            $table->decimal('laba_bruto', 15, 2);
            $table->decimal('total_harga', 15, 2);
            $table->integer('jumlah_penjualan');
            $table->timestamps();

            $table->foreign('id_kasir')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_barang')->references('id')->on('barang')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_pajak')->references('id')->on('pajak_transaksi')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('transaksi_penjualan');
    }
}
