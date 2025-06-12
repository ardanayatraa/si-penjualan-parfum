<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalUmum extends Model
{
    use HasFactory;

    protected $table = 'jurnal_umum';

    protected $fillable = [
        'tanggal',
        'no_bukti',
        'keterangan',
    ];

    public function detailJurnal()
    {
        return $this->hasMany(DetailJurnal::class, 'jurnal_umum_id');
    }

    public function transaksiPenjualan()
    {
        return $this->hasOne(TransaksiPenjualan::class, 'jurnal_umum_id');
    }

    public function transaksiPembelian()
    {
        return $this->hasOne(TransaksiPembelian::class, 'jurnal_umum_id');
    }

    public function returnBarang()
    {
        return $this->hasOne(ReturnBarang::class, 'jurnal_umum_id');
    }
}
