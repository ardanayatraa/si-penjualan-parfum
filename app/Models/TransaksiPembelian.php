<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransaksiPembelian extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_barang',
        'id_supplier',
        'tanggal',
        'nama_barang',
        'harga_beli',
        'jumlah',
        'total_harga_beli',
        'total_nilai_transaksi',
        'keterangan',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier');
    }
}
