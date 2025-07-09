<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hutang extends Model
{
    use HasFactory;
    protected $table = 'hutang';
    protected $primaryKey = 'id_hutang';
    protected $fillable = [
        'id_pembelian',
        'id_supplier',
        'jumlah',
        'tgl_tempo',
        'status',
    ];

    public function supplier()
    {
        return $this->belongsTo(\App\Models\Supplier::class, 'id_supplier', 'id_supplier');
    }

    public function pembelian()
    {
        return $this->belongsTo(\App\Models\TransaksiPembelian::class, 'id_pembelian', 'id_pembelian');
    }
}
