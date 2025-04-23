<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('pajak_transaksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_transaksi');
            $table->enum('jenis_transaksi', ['penjualan', 'pembelian']);
            $table->decimal('persentase_pajak', 5, 2);
            $table->decimal('nilai_pajak', 15, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pajak_transaksi');
    }
};
