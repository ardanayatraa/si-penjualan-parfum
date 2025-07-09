<?php

// Model Akun
// File: app/Models/Akun.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Akun extends Model
{
    use HasFactory;

    protected $table = 'akun';
    protected $primaryKey = 'id_akun';
    public $timestamps = true;

    protected $fillable = [
        'kode_akun',
        'nama_akun',
        'tipe_akun',
        'kategori_akun',
        'saldo_awal',
    ];

    protected $casts = [
        'saldo_awal' => 'decimal:2',
    ];

    // Relationship dengan Jurnal Umum
    public function jurnalUmum()
    {
        return $this->hasMany(JurnalUmum::class, 'id_akun', 'id_akun');
    }

    // Relationship dengan Pengeluaran
    public function pengeluaran()
    {
        return $this->hasMany(Pengeluaran::class, 'id_akun', 'id_akun');
    }

    // Scope untuk filter berdasarkan tipe akun
    public function scopeByTipe($query, $tipe)
    {
        return $query->where('tipe_akun', $tipe);
    }

    // Scope untuk filter berdasarkan kategori akun
    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori_akun', $kategori);
    }

    // Method untuk mendapatkan saldo pada tanggal tertentu
    public function getSaldoOnDate($date)
    {
        $totalDebit = $this->jurnalUmum()
            ->where('tanggal', '<=', $date)
            ->sum('debit');

        $totalKredit = $this->jurnalUmum()
            ->where('tanggal', '<=', $date)
            ->sum('kredit');

        // Hitung saldo berdasarkan tipe akun
        if (in_array($this->tipe_akun, ['Aset', 'Beban'])) {
            // Saldo normal debit
            return $this->saldo_awal + $totalDebit - $totalKredit;
        } else {
            // Saldo normal kredit
            return $this->saldo_awal + $totalKredit - $totalDebit;
        }
    }

    // Method untuk mendapatkan mutasi dalam periode tertentu
    public function getMutasiInPeriod($startDate, $endDate)
    {
        return $this->jurnalUmum()
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get();
    }
}
