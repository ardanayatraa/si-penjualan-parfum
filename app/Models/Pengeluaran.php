<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    use HasFactory;

    protected $table = 'pengeluaran';
    protected $primaryKey = 'id_pengeluaran';
    public $timestamps = true;

    protected $fillable = [
        'id_akun',
        'id_user',
        'tanggal',
        'jenis_pengeluaran',
        'jumlah',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'decimal:2',
    ];

    // Relationship dengan Akun
    public function akun()
    {
        return $this->belongsTo(Akun::class, 'id_akun', 'id_akun');
    }

    // Relationship dengan User
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }


    // Scope untuk filter berdasarkan tanggal
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('tanggal', $date);
    }

    // Scope untuk filter berdasarkan periode
    public function scopeByPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal', [$startDate, $endDate]);
    }

    // Scope untuk filter berdasarkan jenis pengeluaran
    public function scopeByJenis($query, $jenis)
    {
        return $query->where('jenis_pengeluaran', $jenis);
    }
}
