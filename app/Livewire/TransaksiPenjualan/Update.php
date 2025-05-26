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

    // input
    public $id_barang;
    public $jumlah_penjualan;
    public $tanggal_transaksi;
    public $id_pajak;

    // lookup
    public $harga_jual  = 0;
    public $harga_pokok = 0;

    // computed
    public $subtotal;
    public $laba_bruto;
    public $total_harga;

    protected $listeners = ['edit' => 'loadData'];

    protected $rules = [
        'id_barang'        => 'required|exists:barang,id',
        'jumlah_penjualan' => 'required|integer|min:1',
        'tanggal_transaksi'=> 'required|date',
        'id_pajak'         => 'required|exists:pajak_transaksi,id',
    ];

    public function loadData($id)
    {
        $t = TransaksiPenjualan::findOrFail($id);

        $this->id_transaksi     = $t->id;
        $this->id_barang        = $t->id_barang;
        $this->jumlah_penjualan = $t->jumlah_penjualan;
        $this->tanggal_transaksi= $t->tanggal_transaksi;
        $this->id_pajak         = $t->id_pajak;

        // lookup harga jual & pokok from Barang
        if ($b = Barang::find($this->id_barang)) {
            $this->harga_jual  = $b->harga_jual;
            $this->harga_pokok = $b->harga_beli;
        }

        // load stored computations
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
        } else {
            $this->harga_jual = $this->harga_pokok = 0;
        }
        $this->recalculate();
    }

    public function updatedJumlahPenjualan($value)
    {
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

        // 2) Laba bruto = subtotal - (harga_pokok * jumlah_penjualan)
        $this->laba_bruto = max(
            0,
            $this->subtotal - ($this->harga_pokok * $this->jumlah_penjualan)
        );

        // 3) Total harga = subtotal + pajak
        if ($p = PajakTransaksi::find($this->id_pajak)) {
            $this->total_harga = round(
                $this->subtotal * (1 + $p->presentase / 100),
                2
            );
        } else {
            $this->total_harga = $this->subtotal;
        }
    }

    public function update()
    {
        $this->validate();

        TransaksiPenjualan::where('id', $this->id_transaksi)
            ->update([
                'id_barang'          => $this->id_barang,
                'jumlah_penjualan'   => $this->jumlah_penjualan,
                'tanggal_transaksi'  => $this->tanggal_transaksi,
                'id_pajak'           => $this->id_pajak,
                'subtotal'           => $this->subtotal,
                'harga_pokok'        => $this->harga_pokok * $this->jumlah_penjualan,
                'laba_bruto'         => $this->laba_bruto,
                'total_harga'        => $this->total_harga,
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
