<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('transaksi_pembelian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_barang')
                  ->constrained('barang')
                  ->onDelete('restrict');
            $table->date('tanggal_transaksi');
            $table->integer('jumlah_pembelian')->default(1);
            $table->decimal('total', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transaksi_pembelian');
    }
};
