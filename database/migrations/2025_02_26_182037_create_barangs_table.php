<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('barang', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_supplier');
            $table->foreign('id_supplier')
                  ->references('id_supplier')
                  ->on('supplier')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();
            $table->string('nama_barang', 30);
            $table->string('satuan', 20);
            $table->integer('harga_beli');
            $table->integer('harga_jual');
            $table->integer('stok')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('barang');
    }
};
