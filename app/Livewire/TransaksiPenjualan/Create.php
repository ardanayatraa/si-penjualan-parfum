<?php

namespace App\Livewire\TransaksiPenjualan;

use Livewire\Component;
use App\Models\Barang;
use App\Models\PajakTransaksi;
use App\Models\TransaksiPenjualan;
use Illuminate\Support\Facades\Auth;

class Create extends Component
{
    public $open = false;

    // Input fields
    public $id_barang;
    public $jumlah_penjualan = 1;
    public $tanggal_transaksi;
    public $id_pajak;

    // Lookup values
    public $harga_jual  = 0;
    public $harga_pokok = 0;

    // Computed
    public $subtotal    = 0;
    public $laba_bruto  = 0;
    public $total_harga = 0;

    protected $rules = [
        'id_barang'        => 'required|exists:barang,id',
        'jumlah_penjualan' => 'required|integer|min:1',
        'tanggal_transaksi'=> 'required|date',
        'id_pajak'         => 'required|exists:pajak_transaksi,id',
    ];

    public function updatedIdBarang($value)
    {
        $b = Barang::find($value);
        if ($b) {
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
        $this->laba_bruto = max(0, $this->subtotal - ($this->harga_pokok * $this->jumlah_penjualan));

        // 3) Total harga = subtotal + pajak
        if ($p = PajakTransaksi::find($this->id_pajak)) {
            $this->total_harga = round($this->subtotal * (1 + $p->presentase / 100), 2);
        } else {
            $this->total_harga = $this->subtotal;
        }
    }

    public function store()
    {
        $this->validate();

        TransaksiPenjualan::create([
            'id_kasir'           => Auth::id(),
            'id_barang'          => $this->id_barang,
            'jumlah_penjualan'   => $this->jumlah_penjualan,
            'tanggal_transaksi'  => $this->tanggal_transaksi,
            'id_pajak'           => $this->id_pajak,
            'subtotal'           => $this->subtotal,
            'harga_pokok'        => $this->harga_pokok * $this->jumlah_penjualan,
            'laba_bruto'         => $this->laba_bruto,
            'total_harga'        => $this->total_harga,
        ]);

        $this->reset([
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
        $this->dispatch('refreshDatatable');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.transaksi-penjualan.create', [
            'listBarang' => Barang::all(),
            'listPajak'  => PajakTransaksi::all(),
        ]);
    }
}
