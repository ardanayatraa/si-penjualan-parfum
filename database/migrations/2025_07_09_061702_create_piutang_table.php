<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePiutangTable extends Migration
{
    public function up()
    {
        Schema::create('piutang', function (Blueprint $table) {
            $table->id('id_piutang');
            $table->unsignedBigInteger('id_penjualan');
            $table->decimal('jumlah', 15, 2);
            $table->string('status', 20);
            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('piutang');
    }
}
