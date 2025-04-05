<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PajakTransaksi extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_transaksi',
        'jenis_transaksi',
        'persentase_pajak',
        'nilai_pajak',
    ];

    public function transaksiPenjualan()
    {
        return $this->belongsTo(TransaksiPenjualan::class, 'id_transaksi');
    }
}
