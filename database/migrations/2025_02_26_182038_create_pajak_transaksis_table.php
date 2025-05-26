<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('pajak_transaksi', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->decimal('presentase', 5, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pajak_transaksi');
    }
};
