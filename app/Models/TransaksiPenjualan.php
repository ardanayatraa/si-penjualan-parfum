<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransaksiPenjualan extends Model
{
    use HasFactory;

    protected $table = 'transaksi_penjualan';

    protected $fillable = [
        'id_kasir',
        'id_barang',
        'id_pajak',
        'tanggal_transaksi',
        'jumlah_terjual',        // Sesuai migration yang baru
        'subtotal',
        'harga_pokok',
        'laba_bruto',
        'total_harga',
        'metode_pembayaran',     // Field baru
        'status',                // Field baru
    ];

    protected $casts = [
        'tanggal_transaksi' => 'datetime',
        'subtotal' => 'decimal:2',
        'harga_pokok' => 'decimal:2',
        'laba_bruto' => 'decimal:2',
        'total_harga' => 'decimal:2',
    ];

    protected $attributes = [
        'status' => 'pending',
    ];

    // Scopes untuk query yang sering digunakan
    public function scopeSelesai($query)
    {
        return $query->where('status', 'selesai');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeHariIni($query)
    {
        return $query->whereDate('tanggal_transaksi', today());
    }

    public function scopePeriode($query, $start, $end)
    {
        return $query->whereBetween('tanggal_transaksi', [$start, $end]);
    }

    // Relationships
    public function kasir()
    {
        return $this->belongsTo(User::class, 'id_kasir');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_kasir');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }

    public function pajak()
    {
        return $this->belongsTo(PajakTransaksi::class, 'id_pajak');
    }

    public function piutang()
    {
        return $this->hasOne(Piutang::class, 'id_penjualan');
    }

    // Accessor untuk menghitung pajak amount
    public function getPajakAmountAttribute()
    {
        if ($this->pajak) {
            return ($this->subtotal * $this->pajak->presentase) / 100;
        }
        return 0;
    }

    // Accessor untuk format mata uang
    public function getFormattedSubtotalAttribute()
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    public function getFormattedTotalHargaAttribute()
    {
        return 'Rp ' . number_format($this->total_harga, 0, ',', '.');
    }

    public function getFormattedLabaBrutoAttribute()
    {
        return 'Rp ' . number_format($this->laba_bruto, 0, ',', '.');
    }

    // Mutator untuk menghitung otomatis laba bruto
    public function setLabaBrutoAttribute($value)
    {
        // Jika tidak diisi manual, hitung otomatis
        if (is_null($value) && $this->subtotal && $this->harga_pokok) {
            $this->attributes['laba_bruto'] = $this->subtotal - ($this->harga_pokok * $this->jumlah_terjual);
        } else {
            $this->attributes['laba_bruto'] = $value;
        }
    }

    // Method untuk menghitung total otomatis
    public function hitungTotal()
    {
        $pajakAmount = $this->pajak_amount;
        $this->total_harga = $this->subtotal + $pajakAmount;
        return $this;
    }

    // Method untuk menghitung laba bruto otomatis
    public function hitungLabaBruto()
    {
        if ($this->subtotal && $this->harga_pokok && $this->jumlah_terjual) {
            $this->laba_bruto = $this->subtotal - ($this->harga_pokok * $this->jumlah_terjual);
        }
        return $this;
    }

    // Method untuk mengubah status
    public function selesaikan()
    {
        $this->update(['status' => 'selesai']);

        // Update stok barang
        if ($this->barang) {
            $this->barang->decrement('stok', $this->jumlah_terjual);
        }

        return $this;
    }

    public function batalkan()
    {
        $this->update(['status' => 'dibatalkan']);
        return $this;
    }

    // Method untuk mendapatkan kode transaksi
    public function getKodeTransaksiAttribute()
    {
        return 'PNJ-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    // Boot method untuk auto-calculate saat saving
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Auto calculate laba bruto jika belum diset
            if (!$model->laba_bruto && $model->subtotal && $model->harga_pokok && $model->jumlah_terjual) {
                $model->laba_bruto = $model->subtotal - ($model->harga_pokok * $model->jumlah_terjual);
            }


            // Auto calculate total harga jika belum diset
            if (!$model->total_harga && $model->subtotal) {
                $pajakAmount = 0;
                if ($model->pajak) {
                    $pajakAmount = ($model->subtotal * $model->pajak->presentase) / 100;
                }
                $model->total_harga = $model->subtotal + $pajakAmount;
            }
        });
    }
}
