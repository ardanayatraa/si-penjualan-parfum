<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransaksiPembelian extends Model
{
    use HasFactory;

    protected $table = 'transaksi_pembelian';

    protected $fillable = [
        'id_barang',
        'tanggal_transaksi',
        'jumlah_pembelian',
        'total',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'datetime',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }

    public function jurnalUmum()
{
    return $this->hasOne(JurnalUmum::class, 'no_bukti', 'PBJ-'.$this->id);
}
}
