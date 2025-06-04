<?php

namespace App\Livewire\TransaksiPenjualan;

use Livewire\Component;
use App\Models\Barang;
use App\Models\PajakTransaksi;
use App\Models\TransaksiPenjualan;

class Update extends Component
{
    public $open = false;
    public $id_transaksi;

    // Input
    public $id_barang;
    public $jumlah_penjualan = 1;
    public $tanggal_transaksi;
    public $id_pajak;

    // Lookup
    public $harga_jual  = 0;
    public $harga_pokok = 0;

    // Computed
    public $subtotal    = 0;
    public $laba_bruto  = 0;
    public $total_harga = 0;

    protected $listeners = ['editTransaksi' => 'loadData'];

    protected $rules = [
        'id_barang'        => 'required|exists:barang,id',
        'jumlah_penjualan' => 'required|integer|min:1',
        'tanggal_transaksi'=> 'required|date',
        'id_pajak'         => 'required|exists:pajak_transaksi,id',
    ];

    public function loadData($id)
    {
        $t = TransaksiPenjualan::findOrFail($id);

        $this->id_transaksi      = $t->id;
        $this->id_barang         = $t->id_barang;
        $this->jumlah_penjualan  = $t->jumlah_penjualan;
        $this->tanggal_transaksi = $t->tanggal_transaksi;
        $this->id_pajak          = $t->id_pajak;

        // Lookup harga jual & pokok
        if ($b = Barang::find($this->id_barang)) {
            $this->harga_jual  = $b->harga_jual;
            $this->harga_pokok = $b->harga_beli;
            // Pastikan jumlah_penjualan tidak melebihi stok saat ini + jumlah lama
            $maxAllow = $b->stok + $t->jumlah_penjualan;
            if ($this->jumlah_penjualan > $maxAllow) {
                $this->jumlah_penjualan = $maxAllow;
            }
        }

        // Load stored computations
        $this->subtotal    = $t->subtotal;
        $this->laba_bruto  = $t->laba_bruto;
        $this->total_harga = $t->total_harga;

        $this->open = true;
    }

    public function updatedIdBarang($value)
    {
        if ($b = Barang::find($value)) {
            $this->harga_jual  = $b->harga_jual;
            $this->harga_pokok = $b->harga_beli;
            // Reset jumlah_penjualan jika melebihi stok
            if ($this->jumlah_penjualan > $b->stok) {
                $this->jumlah_penjualan = $b->stok;
            }
        } else {
            $this->harga_jual = $this->harga_pokok = 0;
        }
        $this->recalculate();
    }

    public function updatedJumlahPenjualan($value)
    {
        $b = Barang::find($this->id_barang);
        $tOld = TransaksiPenjualan::find($this->id_transaksi);
        if ($b) {
            // Hitung sisa stok jika update: stok + jumlah lama
            $maxAllow = $b->stok + ($tOld?->jumlah_penjualan ?? 0);
            if ($value > $maxAllow) {
                $this->addError(
                    'jumlah_penjualan',
                    'Jumlah penjualan tidak boleh melebihi stok tersedia (' . $maxAllow . ').'
                );
                $this->jumlah_penjualan = $maxAllow;
            } else {
                $this->resetErrorBag('jumlah_penjualan');
            }
        }
        $this->recalculate();
    }

    public function updatedIdPajak($value)
    {
        $this->recalculate();
    }

    private function recalculate()
    {
        // 1) Subtotal = harga_jual * jumlah_penjualan
        $this->subtotal = $this->harga_jual * $this->jumlah_penjualan;

        // 2) Harga pokok total = harga_pokok * jumlah_penjualan
        $hargaPokokTotal = $this->harga_pokok * $this->jumlah_penjualan;

        // 3) Laba bruto = subtotal - harga pokok total
        $this->laba_bruto = max(0, $this->subtotal - $hargaPokokTotal);

        // 4) Total harga = subtotal + pajak
        if ($p = PajakTransaksi::find($this->id_pajak)) {
            $this->total_harga = round($this->subtotal * (1 + $p->presentase / 100), 2);
        } else {
            $this->total_harga = $this->subtotal;
        }
    }

    public function update()
    {
        $this->validate();

        $tOld = TransaksiPenjualan::findOrFail($this->id_transaksi);
        $bNew = Barang::find($this->id_barang);
        if (! $bNew) {
            $this->addError('id_barang', 'Barang tidak ditemukan.');
            return;
        }

        // Maksimal stok untuk transaksi update
        $maxAllow = $bNew->stok + $tOld->jumlah_penjualan;
        if ($this->jumlah_penjualan > $maxAllow) {
            $this->addError(
                'jumlah_penjualan',
                'Jumlah penjualan tidak boleh melebihi stok tersedia (' . $maxAllow . ').'
            );
            return;
        }

        $oldBarangId = $tOld->id_barang;
        $oldJumlah   = $tOld->jumlah_penjualan;

        // Jika barang berubah, kembalikan stok barang lama
        if ($oldBarangId !== $this->id_barang) {
            Barang::where('id', $oldBarangId)->increment('stok', $oldJumlah);
        }

        // Sesuaikan stok barang sekarang
        if ($oldBarangId === $this->id_barang) {
            $selisih = $this->jumlah_penjualan - $oldJumlah;
            if ($selisih > 0) {
                $bNew->decrement('stok', $selisih);
            } elseif ($selisih < 0) {
                $bNew->increment('stok', abs($selisih));
            }
        } else {
            $bNew->decrement('stok', $this->jumlah_penjualan);
        }

        // Update transaksi
        $tOld->update([
            'id_barang'         => $this->id_barang,
            'jumlah_penjualan'  => $this->jumlah_penjualan,
            'tanggal_transaksi' => $this->tanggal_transaksi,
            'id_pajak'          => $this->id_pajak,
            'subtotal'          => $this->subtotal,
            'harga_pokok'       => $this->harga_pokok * $this->jumlah_penjualan,
            'laba_bruto'        => $this->laba_bruto,
            'total_harga'       => $this->total_harga,
        ]);

        $this->dispatch('refreshDatatable');
        $this->reset([
            'id_transaksi',
            'id_barang',
            'jumlah_penjualan',
            'tanggal_transaksi',
            'id_pajak',
            'harga_jual',
            'harga_pokok',
            'subtotal',
            'laba_bruto',
            'total_harga',
        ]);
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.transaksi-penjualan.update', [
            'listBarang' => Barang::all(),
            'listPajak'  => PajakTransaksi::all(),
        ]);
    }
}
