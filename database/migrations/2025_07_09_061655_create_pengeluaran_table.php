<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePengeluaranTable extends Migration
{
    public function up()
    {
        Schema::create('pengeluaran', function (Blueprint $table) {
            $table->id('id_pengeluaran');
            $table->unsignedBigInteger('id_akun');
            $table->unsignedBigInteger('id_user');
            $table->date('tanggal');
            $table->string('jenis_pengeluaran', 50);
            $table->decimal('jumlah', 15, 2);
            $table->string('keterangan', 100)->nullable();
            $table->timestamps();


        });
    }

    public function down()
    {
        Schema::dropIfExists('pengeluaran');
    }
}
