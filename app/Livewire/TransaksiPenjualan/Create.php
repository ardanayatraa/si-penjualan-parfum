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
    public $id_barang, $id_pajak, $tanggal_transaksi, $subtotal = 0;
    public $harga_pokok = 0, $laba_bruto = 0, $total_harga = 0;

    protected $rules = [
        'id_barang'          => 'required|exists:barang,id',
        'id_pajak'           => 'required|exists:pajak_transaksi,id',
        'tanggal_transaksi'  => 'required|date',
        'subtotal'           => 'required|numeric|min:0',
    ];

    public function updatedIdBarang()
    {
        if ($b = Barang::find($this->id_barang)) {
            $this->harga_pokok = $b->harga_beli;
            $this->recalculate();
        }
    }

    public function updatedIdPajak()
    {
        $this->recalculate();
    }

    public function updatedSubtotal()
    {
        $this->recalculate();
    }

    private function recalculate()
    {
        // hitung laba bruto
        $this->laba_bruto = max(0, $this->subtotal - $this->harga_pokok);

        // hitung total harga termasuk pajak
        if ($p = PajakTransaksi::find($this->id_pajak)) {
            $this->total_harga = $this->subtotal * (1 + ($p->tarif / 100));
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
            'id_pajak'           => $this->id_pajak,
            'tanggal_transaksi'  => $this->tanggal_transaksi,
            'subtotal'           => $this->subtotal,
            'harga_pokok'        => $this->harga_pokok,
            'laba_bruto'         => $this->laba_bruto,
            'total_harga'        => $this->total_harga,
        ]);

        $this->dispatch('refreshDatatable');
        $this->reset(['id_barang','id_pajak','tanggal_transaksi','subtotal','harga_pokok','laba_bruto','total_harga']);
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
