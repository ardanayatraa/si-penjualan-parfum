<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('transaksi_penjualan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_kasir');
            $table->foreignId('id_barang');
            $table->date('tanggal_transaksi');
            $table->string('jumlah', 15);
            $table->string('harga_jual', 10);
            $table->string('total_harga', 10);
            $table->integer('total_nilai_transaksi');
            $table->integer('laba_bruto');
            $table->integer('laba_bersih');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transaksi_penjualan');
    }
};
