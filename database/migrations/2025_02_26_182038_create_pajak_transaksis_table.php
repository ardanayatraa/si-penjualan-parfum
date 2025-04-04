<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('transaksi_penjualans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_kasir')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_barang')->constrained('barangs')->onDelete('cascade');
            $table->date('tanggal_transaksi');
            $table->string('nama_barang', 50);
            $table->string('jumlah', 15);
            $table->string('harga_barang', 10);
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
        Schema::dropIfExists('transaksi_penjualans');
    }
};
