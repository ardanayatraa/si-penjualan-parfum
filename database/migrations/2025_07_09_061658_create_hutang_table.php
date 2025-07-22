<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHutangTable extends Migration
{
    public function up()
    {
        Schema::create('hutang', function (Blueprint $table) {
            $table->id('id_hutang');
            $table->unsignedBigInteger('id_pembelian');
            $table->unsignedBigInteger('id_supplier');
            $table->decimal('jumlah', 15, 2);
            $table->decimal('jumlah_dibayarkan', 15, 2)->default(0);
            $table->date('tgl_tempo');
            $table->string('status', 20);
            $table->timestamps();


        });
    }

    public function down()
    {
        Schema::dropIfExists('hutang');
    }
}
