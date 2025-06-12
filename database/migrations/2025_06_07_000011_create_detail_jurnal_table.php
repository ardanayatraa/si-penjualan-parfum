<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailJurnalTable extends Migration
{
    public function up()
    {
        Schema::create('detail_jurnal', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('jurnal_umum_id');
            $table->unsignedBigInteger('akun_id');
            $table->decimal('debit', 15, 2)->default(0.00);
            $table->decimal('kredit', 15, 2)->default(0.00);
            $table->timestamps();

            $table->foreign('jurnal_umum_id')->references('id')->on('jurnal_umum')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('akun_id')->references('id')->on('akun')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('detail_jurnal');
    }
}
