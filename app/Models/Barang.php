<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';

    protected $fillable = [
        'id_supplier',
        'nama_barang',
        'satuan',
        'harga_beli',
        'harga_jual',
        'stok',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier');
    }

    public function transaksiPembelian()
    {
        return $this->hasMany(TransaksiPembelian::class, 'id_barang');
    }

    public function transaksiPenjualan()
    {
        return $this->hasMany(TransaksiPenjualan::class, 'id_barang');
    }
}
