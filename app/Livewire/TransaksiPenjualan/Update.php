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
    public $id_barang, $id_pajak, $tanggal_transaksi, $subtotal;
    public $harga_pokok, $laba_bruto, $total_harga;

    protected $listeners = ['edit' => 'loadData'];

    protected $rules = [
        'id_barang'         => 'required|exists:barang,id',
        'id_pajak'          => 'required|exists:pajak_transaksi,id',
        'tanggal_transaksi' => 'required|date',
        'subtotal'          => 'required|numeric|min:0',
    ];

    public function loadData($id)
    {
        $t = TransaksiPenjualan::findOrFail($id);
        $this->id_transaksi     = $t->id;
        $this->id_barang        = $t->id_barang;
        $this->id_pajak         = $t->id_pajak;
        $this->tanggal_transaksi= $t->tanggal_transaksi;
        $this->subtotal         = $t->subtotal;
        $this->harga_pokok      = $t->harga_pokok;
        $this->laba_bruto       = $t->laba_bruto;
        $this->total_harga      = $t->total_harga;
        $this->open             = true;
    }

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
        $this->laba_bruto = max(0, $this->subtotal - $this->harga_pokok);
        if ($p = PajakTransaksi::find($this->id_pajak)) {
            $this->total_harga = $this->subtotal * (1 + ($p->tarif / 100));
        } else {
            $this->total_harga = $this->subtotal;
        }
    }

    public function update()
    {
        $this->validate();

        TransaksiPenjualan::where('id', $this->id_transaksi)->update([
            'id_barang'          => $this->id_barang,
            'id_pajak'           => $this->id_pajak,
            'tanggal_transaksi'  => $this->tanggal_transaksi,
            'subtotal'           => $this->subtotal,
            'harga_pokok'        => $this->harga_pokok,
            'laba_bruto'         => $this->laba_bruto,
            'total_harga'        => $this->total_harga,
        ]);

        $this->dispatch('refreshDatatable');
        $this->reset(['id_transaksi','id_barang','id_pajak','tanggal_transaksi','subtotal','harga_pokok','laba_bruto','total_harga']);
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
