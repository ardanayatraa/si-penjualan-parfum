<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class TransaksiPenjualan extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_kasir',
        'id_barang',
        'tanggal_transaksi',
        'nama_barang',
        'jumlah',
        'harga_barang',
        'total_harga',
        'total_nilai_transaksi',
        'laba_bruto',
        'laba_bersih',
        'keterangan',
    ];

    public function kasir()
    {
        return $this->belongsTo(User::class, 'id_kasir');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }

    public function pajak()
    {
        return $this->hasOne(PajakTransaksi::class, 'id_transaksi');
    }
}
