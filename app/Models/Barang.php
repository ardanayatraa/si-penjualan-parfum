<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';

    protected $fillable = [
        'id_supplier',
        'nama_barang',
        'satuan',
        'harga_beli',
        'harga_jual',
        'stok',
    ];

    protected $casts = [
        'harga_beli' => 'decimal:2',
        'harga_jual' => 'decimal:2',
    ];

    // Relationships
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier', 'id_supplier');
    }

    // Singular forms (existing methods)
    public function transaksiPembelian()
    {
        return $this->hasMany(TransaksiPembelian::class, 'id_barang');
    }

    public function transaksiPenjualan()
    {
        return $this->hasMany(TransaksiPenjualan::class, 'id_barang');
    }

    // Plural forms (for compatibility with table queries)
    public function transaksiPembelians()
    {
        return $this->hasMany(TransaksiPembelian::class, 'id_barang');
    }

    public function transaksiPenjualans()
    {
        return $this->hasMany(TransaksiPenjualan::class, 'id_barang');
    }

    public function returnBarangs()
    {
        return $this->hasMany(ReturnBarang::class, 'id_barang');
    }

    // Accessors
    public function getFormattedHargaBeliAttribute()
    {
        return 'Rp ' . number_format($this->harga_beli, 0, ',', '.');
    }

    public function getFormattedHargaJualAttribute()
    {
        return 'Rp ' . number_format($this->harga_jual, 0, ',', '.');
    }

    public function getMarginAttribute()
    {
        if ($this->harga_beli > 0) {
            return (($this->harga_jual - $this->harga_beli) / $this->harga_beli) * 100;
        }
        return 0;
    }

    public function getFormattedMarginAttribute()
    {
        return number_format($this->margin, 2) . '%';
    }

    public function getNilaiStokAttribute()
    {
        return $this->stok * $this->harga_beli;
    }

    public function getFormattedNilaiStokAttribute()
    {
        return 'Rp ' . number_format($this->nilai_stok, 0, ',', '.');
    }

    public function getStatusStokAttribute()
    {
        return match(true) {
            $this->stok == 0 => 'habis',
            $this->stok <= 10 => 'menipis',
            default => 'aman'
        };
    }

    public function getStatusStokBadgeAttribute()
    {
        $badgeClass = match($this->status_stok) {
            'habis' => 'bg-red-100 text-red-800',
            'menipis' => 'bg-yellow-100 text-yellow-800',
            'aman' => 'bg-green-100 text-green-800',
        };

        return '<span class="px-2 py-1 text-xs rounded-full ' . $badgeClass . '">' . $this->stok . '</span>';
    }

    // Scopes
    public function scopeInStock($query)
    {
        return $query->where('stok', '>', 0);
    }

    public function scopeLowStock($query, $threshold = 10)
    {
        return $query->where('stok', '<=', $threshold)->where('stok', '>', 0);
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('stok', 0);
    }

    public function scopeBySupplier($query, $supplierId)
    {
        return $query->where('id_supplier', $supplierId);
    }

    public function scopeWithTransactionSummary($query)
    {
        return $query->withSum(['transaksiPembelians as total_pembelian' => function($query) {
            $query->where('status', 'selesai');
        }], 'jumlah_pembelian')
        ->withSum(['transaksiPenjualans as total_penjualan' => function($query) {
            $query->where('status', 'selesai');
        }], 'jumlah_terjual');
    }

    // Methods
    public function updateStok($jumlah, $operasi = 'tambah')
    {
        if ($operasi === 'tambah') {
            $this->increment('stok', $jumlah);
        } elseif ($operasi === 'kurang') {
            $this->decrement('stok', $jumlah);
        }

        return $this;
    }

    public function getTurnoverRate()
    {
        $totalPembelian = $this->transaksiPembelians()
            ->where('status', 'selesai')
            ->sum('jumlah_pembelian');

        $totalPenjualan = $this->transaksiPenjualans()
            ->where('status', 'selesai')
            ->sum('jumlah_terjual');

        if ($totalPembelian > 0) {
            return ($totalPenjualan / $totalPembelian) * 100;
        }

        return 0;
    }

    public function getFormattedTurnoverRateAttribute()
    {
        return number_format($this->getTurnoverRate(), 1) . '%';
    }

    public function isStokMenipis($threshold = 10)
    {
        return $this->stok <= $threshold && $this->stok > 0;
    }

    public function isStokHabis()
    {
        return $this->stok == 0;
    }

    public function isStokAman($threshold = 10)
    {
        return $this->stok > $threshold;
    }
}
