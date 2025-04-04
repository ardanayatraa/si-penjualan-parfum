<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_barang', 30);
            $table->integer('harga_beli');
            $table->integer('harga_jual');
            $table->integer('jumlah_retur')->default(0);
            $table->integer('jumlah_terjual')->default(0);
            $table->integer('jumlah_stok')->default(0);
            $table->integer('jumlah_nilai_stok')->default(0);
            $table->string('keterangan', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('barangs');
    }
};
