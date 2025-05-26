<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('transaksi_penjualan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_kasir')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->foreignId('id_barang')
                  ->constrained('barang')
                  ->onDelete('restrict');
            $table->foreignId('id_pajak')
                  ->constrained('pajak_transaksi')
                  ->onDelete('restrict');
            $table->date('tanggal_transaksi');
            $table->decimal('subtotal', 15, 2);
            $table->decimal('harga_pokok', 15, 2);
            $table->decimal('laba_bruto', 15, 2);
            $table->decimal('total_harga', 15, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transaksi_penjualan');
    }
};
