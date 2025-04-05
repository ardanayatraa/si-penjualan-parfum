<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'suppliers';
    protected $primaryKey = 'id_supplier';

    protected $fillable = [
        'nama_supplier',
        'alamat',
        'no_telp',
    ];

    public function transaksiPembelian()
    {
        return $this->hasMany(TransaksiPembelian::class, 'id_supplier');
    }
}
