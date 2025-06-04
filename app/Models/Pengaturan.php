<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    use HasFactory;

    protected $table = 'pengaturan';

    protected $primaryKey = 'nama_pengaturan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nama_pengaturan',
        'nilai_pengaturan',
        'keterangan',
    ];
}
