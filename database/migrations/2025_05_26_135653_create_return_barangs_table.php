<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('return_barang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_barang')
                  ->constrained('barang')
                  ->onDelete('restrict');
            $table->foreignId('id_supplier');
            $table->integer('jumlah');
            $table->text('alasan');
            $table->date('tanggal_return');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('return_barang');
    }
};
