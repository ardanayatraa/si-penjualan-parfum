<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Piutang extends Model
{
    use HasFactory;
    protected $table = 'piutang';
    protected $primaryKey = 'id_piutang';
    protected $fillable = [
        'id_penjualan',
        'jumlah',
        'jumlah_dibayarkan',
        'status',
        'nama_pelanggan', // Tambahan untuk menyimpan nama pelanggan
        'no_telp',        // Tambahan untuk menyimpan nomor telepon pelanggan
    ];

    public function penjualan()
    {
        return $this->belongsTo(\App\Models\TransaksiPenjualan::class, 'id_penjualan', 'id');
    }
}
