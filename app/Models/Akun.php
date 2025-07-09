<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Akun extends Model
{
    use HasFactory;

    protected $table = 'akun';
    protected $primaryKey = 'id_akun';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'kode_akun',
        'nama_akun',
        'tipe_akun',
        'kategori_akun',
        'saldo_awal',
    ];

    /**
     * Relasi ke model Pengeluaran
     * Satu akun bisa punya banyak pengeluaran
     */
    public function pengeluarans()
    {
        return $this->hasMany(Pengeluaran::class, 'akun_id', 'id_akun');
    }
}
