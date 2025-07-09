<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJurnalUmumTable extends Migration
{
    public function up()
    {
        Schema::create('jurnal_umum', function (Blueprint $table) {
            $table->bigIncrements('id_jurnal');
            $table->unsignedBigInteger('id_akun');
            $table->date('tanggal');
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('kredit', 15, 2)->default(0);
            $table->string('keterangan', 100)->nullable();
            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('jurnal_umum');
    }
}
