<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransaksiPenjualan extends Model
{
    use HasFactory;

    protected $table = 'transaksi_penjualan';

    protected $fillable = [
        'id_kasir',
        'id_barang',
        'id_pajak',
        'tanggal_transaksi',
        'subtotal',
        'harga_pokok',
        'laba_bruto',
        'total_harga',
        'jumlah_penjualan',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'datetime',
    ];

    public function kasir()
    {
        return $this->belongsTo(User::class, 'id_kasir')->where('level', 'kasir');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }

    public function pajak()
    {
        return $this->belongsTo(PajakTransaksi::class, 'id_pajak');
    }

    public function jurnalUmum()
    {
        return $this->hasOne(JurnalUmum::class, 'no_bukti', 'PNJ-'.$this->id);
    }

}
