<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReturnBarang extends Model
{
    use HasFactory;

    protected $table = 'return_barang';

    protected $fillable = [
        'id_barang',
        'id_supplier',
        'jumlah',
        'alasan',
        'tanggal_return',
        'jurnal_umum_id'
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier');
    }

    public function transaksi()
{
    return $this->belongsTo(TransaksiPembelian::class, 'id_transaksi_pembelian');
}
}
