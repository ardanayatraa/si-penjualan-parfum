<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('transaksi_pembelian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_barang');
            $table->foreignId('id_supplier');
            $table->date('tanggal');
            $table->string('nama_barang', 20);
            $table->integer('harga_beli');
            $table->integer('jumlah');
            $table->integer('total_harga_beli');
            $table->integer('total_nilai_transaksi');
            $table->string('keterangan', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transaksi_pembelian');
    }
};
