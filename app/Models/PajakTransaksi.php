<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PajakTransaksi extends Model
{
    use HasFactory;

    protected $table = 'pajak_transaksi';

    protected $fillable = [
        'nama',
        'presentase',
    ];
}
