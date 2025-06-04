<?php
// database/migrations/2025_06_03_000000_create_pengaturan_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('pengaturan', function (Blueprint $table) {

            $table->string('nama_pengaturan')->primary();
            $table->bigInteger('nilai_pengaturan');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pengaturan');
    }
};
