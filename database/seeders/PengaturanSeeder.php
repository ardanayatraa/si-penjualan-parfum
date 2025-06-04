<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pengaturan;

class PengaturanSeeder extends Seeder
{
    public function run()
    {
        Pengaturan::updateOrCreate(
            ['nama_pengaturan' => 'modal_awal'],
            [
                'nilai_pengaturan' => 10000000,
                'keterangan'       => 'Nilai modal awal usaha parfum',
            ]
        );
    }
}
