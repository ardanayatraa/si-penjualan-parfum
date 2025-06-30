<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PajakTransaksiSeeder extends Seeder
{
    public function run(): void
    {

        DB::table('pajak_transaksi')->insert([
            'id' => 1,
            'nama' => 'PPN 11%',
            'presentase' => 11,
        ]);
    }
}
