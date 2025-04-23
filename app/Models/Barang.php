<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Barang extends Model
{
    use HasFactory;
    protected $table='barang';
    protected $fillable = [
        'nama_barang',
        'harga_beli',
        'harga_jual',
        'jumlah_retur',
        'jumlah_terjual',
        'jumlah_stok',
        'jumlah_nilai_stok',
        'keterangan',
    ];

    public function transaksiPembelian()
    {
        return $this->hasMany(TransaksiPembelian::class, 'id_barang');
    }

    public function transaksiPenjualan()
    {
        return $this->hasMany(TransaksiPenjualan::class, 'id_barang');
    }
}
