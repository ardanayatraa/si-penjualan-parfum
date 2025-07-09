<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAkunTable extends Migration
{
    public function up()
    {
        Schema::create('akun', function (Blueprint $table) {
            $table->increments('id_akun'); // sesuai dengan ERD
            $table->string('kode_akun', 10)->unique(); // maksimal 10 karakter
            $table->string('nama_akun', 50);
            $table->string('tipe_akun', 50); // tidak lagi enum agar fleksibel
            $table->string('kategori_akun', 50)->nullable();
            $table->decimal('saldo_awal', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('akun');
    }
}
