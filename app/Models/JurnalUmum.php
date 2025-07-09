<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalUmum extends Model
{
    use HasFactory;

    protected $table = 'jurnal_umum';
    protected $primaryKey = 'id_jurnal';

    protected $fillable = [
        'id_akun',
        'tanggal',
        'debit',
        'kredit',
        'keterangan',
    ];

    public function akun()
    {
        return $this->belongsTo(Akun::class, 'id_akun', 'id_akun');
    }
}
