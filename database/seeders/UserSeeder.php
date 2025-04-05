<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'level' => 'admin',
                'username' => 'admin01',
                'password' => Hash::make('password123'),
                'no_telp' => '081234567890',
                'alamat' => 'Denpasar',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'level' => 'kasir',
                'username' => 'kasir01',
                'password' => Hash::make('password123'),
                'no_telp' => '081298765432',
                'alamat' => 'Gianyar',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'level' => 'pemilik',
                'username' => 'owner01',
                'password' => Hash::make('password123'),
                'no_telp' => '081377788899',
                'alamat' => 'Badung',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
