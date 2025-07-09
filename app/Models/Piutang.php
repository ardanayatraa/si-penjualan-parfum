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
        'status',
    ];

    public function penjualan()
    {
        return $this->belongsTo(\App\Models\TransaksiPenjualan::class, 'id_penjualan', 'id_penjualan');
    }
} 