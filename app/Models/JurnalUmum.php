<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalUmum extends Model
{
    use HasFactory;

    protected $table = 'jurnal_umum';
    protected $primaryKey = 'id_jurnal';
    public $timestamps = true;

    protected $fillable = [
        'id_akun',
        'tanggal',
        'debit',
        'kredit',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'debit' => 'decimal:2',
        'kredit' => 'decimal:2',
    ];

    // Relationship dengan Akun
    public function akun()
    {
        return $this->belongsTo(Akun::class, 'id_akun', 'id_akun');
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

    // Scope untuk filter berdasarkan akun
    public function scopeByAkun($query, $idAkun)
    {
        return $query->where('id_akun', $idAkun);
    }

    // Scope untuk hanya debit
    public function scopeDebitOnly($query)
    {
        return $query->where('debit', '>', 0);
    }

    // Scope untuk hanya kredit
    public function scopeKreditOnly($query)
    {
        return $query->where('kredit', '>', 0);
    }

    // Method untuk mendapatkan saldo berjalan
    public function getRunningSaldo($akunId, $tipeAkun, $saldoAwal)
    {
        $prevEntries = self::where('id_akun', $akunId)
            ->where('tanggal', '<=', $this->tanggal)
            ->where('id_jurnal', '<=', $this->id_jurnal)
            ->get();

        $runningSaldo = $saldoAwal;

        foreach ($prevEntries as $entry) {
            if (in_array($tipeAkun, ['Aset', 'Beban'])) {
                $runningSaldo += $entry->debit - $entry->kredit;
            } else {
                $runningSaldo += $entry->kredit - $entry->debit;
            }
        }

        return $runningSaldo;
    }

    // Method untuk validasi jurnal (debit = kredit)
    public static function validateJurnalBalance($entries)
    {
        $totalDebit = collect($entries)->sum('debit');
        $totalKredit = collect($entries)->sum('kredit');

        return $totalDebit == $totalKredit;
    }
}

