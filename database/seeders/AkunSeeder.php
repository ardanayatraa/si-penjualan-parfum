<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AkunSeeder extends Seeder
{
    public function run()
    {
        // Kosongkan tabel akun
        DB::table('akun')->truncate();

        $akunList = [
            // === ASET ===
            ['kode' => '1.1.00', 'nama' => 'Aset Lancar',        'tipe' => 'aset',       'kategori' => 'Kas & Setara Kas',       'saldo' => 0],
            ['kode' => '1.1.01', 'nama' => 'Kas',                'tipe' => 'aset',       'kategori' => 'Kas & Setara Kas',       'saldo' => 10000000],
            ['kode' => '1.1.05', 'nama' => 'Persediaan Barang',  'tipe' => 'aset',       'kategori' => 'Persediaan',             'saldo' => 0],
            ['kode' => '1.2.01', 'nama' => 'Piutang Dagang',     'tipe' => 'aset',       'kategori' => 'Piutang',                'saldo' => 0], // Tambah untuk penjualan piutang

            // === KEWAJIBAN ===
            ['kode' => '2.1.00', 'nama' => 'Kewajiban Lancar',   'tipe' => 'kewajiban',  'kategori' => 'Utang Usaha',            'saldo' => 0],
            ['kode' => '2.1.01', 'nama' => 'Hutang Dagang',      'tipe' => 'kewajiban',  'kategori' => 'Utang Usaha',            'saldo' => 0],
            ['kode' => '2.1.02', 'nama' => 'PPN Keluaran',       'tipe' => 'kewajiban',  'kategori' => 'Pajak',                  'saldo' => 0],

            // === MODAL ===
            ['kode' => '3.1.01', 'nama' => 'Modal Pemilik',      'tipe' => 'modal',      'kategori' => 'Modal',                  'saldo' => 15000000], // Tambah untuk neraca balance

            // === PENDAPATAN ===
            ['kode' => '4.1.00', 'nama' => 'Pendapatan',         'tipe' => 'pendapatan', 'kategori' => 'Penjualan',              'saldo' => 0],
            ['kode' => '4.1.01', 'nama' => 'Penjualan Barang',   'tipe' => 'pendapatan', 'kategori' => 'Penjualan',              'saldo' => 0],

            // === BEBAN ===
            ['kode' => '5.1.00', 'nama' => 'Beban',              'tipe' => 'beban',      'kategori' => 'Biaya Pokok Penjualan',  'saldo' => 0],
            ['kode' => '5.1.03', 'nama' => 'Beban HPP',          'tipe' => 'beban',      'kategori' => 'Biaya Pokok Penjualan',  'saldo' => 0],
        ];

        foreach ($akunList as $data) {
            DB::table('akun')->insert([
                'kode_akun'     => $data['kode'],
                'nama_akun'     => $data['nama'],
                'tipe_akun'     => $data['tipe'],
                'kategori_akun' => $data['kategori'],
                'saldo_awal'    => $data['saldo'],
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }
    }
}