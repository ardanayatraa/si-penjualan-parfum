<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransaksiPembelian extends Model
{
    use HasFactory;

    protected $table = 'transaksi_pembelian';

    protected $fillable = [
        'id_barang',
        'id_supplier',
        'tanggal_transaksi',
        'jumlah_pembelian',
        'harga',
        'total',
        'metode_pembayaran',
        'status',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'datetime',
        'harga' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    protected $attributes = [
        'metode_pembayaran' => 'cash',
        'status' => 'pending',
    ];

    // Scopes
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
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier');
    }

    public function hutang()
    {
        return $this->hasOne(Hutang::class, 'id_pembelian');
    }

    // Accessors
    public function getFormattedHargaAttribute()
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }

    public function getFormattedTotalAttribute()
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }

    public function getKodeTransaksiAttribute()
    {
        return 'PBL-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    // Mutators
    public function setTotalAttribute($value)
    {
        // Auto calculate jika tidak diisi manual
        if (is_null($value) && $this->harga && $this->jumlah_pembelian) {
            $this->attributes['total'] = $this->harga * $this->jumlah_pembelian;
        } else {
            $this->attributes['total'] = $value;
        }
    }

    // Methods
    public function hitungTotal()
    {
        if ($this->harga && $this->jumlah_pembelian) {
            $this->total = $this->harga * $this->jumlah_pembelian;
        }
        return $this;
    }

    public function selesaikan()
    {
        $this->update(['status' => 'selesai']);

        // Update stok barang
        if ($this->barang) {
            $this->barang->increment('stok', $this->jumlah_pembelian);

            // Update harga beli barang dengan harga pembelian terbaru
            $this->barang->update(['harga_beli' => $this->harga]);
        }

        return $this;
    }

    public function batalkan()
    {
        $this->update(['status' => 'dibatalkan']);
        return $this;
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Auto calculate total jika belum diset
            if (!$model->total && $model->harga && $model->jumlah_pembelian) {
                $model->total = $model->harga * $model->jumlah_pembelian;
            }
        });
    }
}
