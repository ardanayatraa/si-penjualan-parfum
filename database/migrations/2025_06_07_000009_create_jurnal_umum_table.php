<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJurnalUmumTable extends Migration
{
    public function up()
    {
        Schema::create('jurnal_umum', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('tanggal');
            $table->string('no_bukti', 30)->unique();
            $table->string('keterangan', 255);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('jurnal_umum');
    }
}
