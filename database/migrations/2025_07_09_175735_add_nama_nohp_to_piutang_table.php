<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNamaNohpToPiutangTable extends Migration
{
    public function up()
    {
        Schema::table('piutang', function (Blueprint $table) {
            $table->string('nama_pelanggan', 50)->nullable()->after('id_penjualan');
            $table->string('no_telp', 15)->nullable()->after('nama_pelanggan');
        });
    }

    public function down()
    {
        Schema::table('piutang', function (Blueprint $table) {
            $table->dropColumn(['nama_pelanggan', 'no_telp']);
        });
    }
}
