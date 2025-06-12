<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AkunSeeder extends Seeder
{
    public function run()
    {
        // Nonaktifkan cek foreign key, lalu kosongkan tabel akun
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('akun')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Hanya akun yang digunakan di transaksi pembelian & penjualan
        $akunList = [
            // Parent akun
            ['kode' => '1.1.00', 'nama' => 'Aset Lancar',       'tipe' => 'aset',       'parent_kode' => null],
            ['kode' => '2.1.00', 'nama' => 'Kewajiban Lancar',  'tipe' => 'kewajiban',  'parent_kode' => null],
            ['kode' => '4.1.00', 'nama' => 'Pendapatan',        'tipe' => 'pendapatan', 'parent_kode' => null],
            ['kode' => '5.1.00', 'nama' => 'Beban',             'tipe' => 'beban',      'parent_kode' => null],

            // Child akun
            ['kode' => '1.1.01', 'nama' => 'Kas',               'tipe' => 'aset',       'parent_kode' => '1.1.00'],
            ['kode' => '1.1.05', 'nama' => 'Persediaan Barang', 'tipe' => 'aset',       'parent_kode' => '1.1.00'],

            ['kode' => '2.1.01', 'nama' => 'Hutang Dagang',     'tipe' => 'kewajiban',  'parent_kode' => '2.1.00'],
            ['kode' => '2.1.02', 'nama' => 'PPN Keluaran',      'tipe' => 'kewajiban',  'parent_kode' => '2.1.00'],

            ['kode' => '4.1.01', 'nama' => 'Penjualan Barang',  'tipe' => 'pendapatan', 'parent_kode' => '4.1.00'],

            ['kode' => '5.1.03', 'nama' => 'Beban HPP',         'tipe' => 'beban',      'parent_kode' => '5.1.00'],
        ];

        $map = [];
        // Insert parent
        foreach ($akunList as $data) {
            if (is_null($data['parent_kode'])) {
                $map[$data['kode']] = DB::table('akun')->insertGetId([
                    'kode_akun' => $data['kode'],
                    'nama_akun' => $data['nama'],
                    'tipe_akun' => $data['tipe'],
                    'parent_id' => null,
                    'created_at'=> now(),
                    'updated_at'=> now(),
                ]);
            }
        }
        // Insert children
        foreach ($akunList as $data) {
            if (!is_null($data['parent_kode'])) {
                DB::table('akun')->insert([
                    'kode_akun' => $data['kode'],
                    'nama_akun' => $data['nama'],
                    'tipe_akun' => $data['tipe'],
                    'parent_id' => $map[$data['parent_kode']],
                    'created_at'=> now(),
                    'updated_at'=> now(),
                ]);
            }
        }
    }
}
