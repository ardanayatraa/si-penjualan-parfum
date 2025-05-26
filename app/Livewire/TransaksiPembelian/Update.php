<?php

namespace App\Livewire\TransaksiPembelian;

use Livewire\Component;
use App\Models\TransaksiPembelian;
use App\Models\Barang;
use App\Models\Supplier;

class Update extends Component
{
    public $open = false;
    public $transaksiId;
    public $id_barang, $id_supplier, $tanggal_transaksi, $jumlah_pembelian, $total;
    public $harga_beli = 0;

    protected $listeners = ['editTransaksi' => 'loadData'];

    protected $rules = [
        'id_barang'           => 'required|exists:barang,id',
        'id_supplier'         => 'required|exists:supplier,id_supplier',
        'tanggal_transaksi'   => 'required|date',
        'jumlah_pembelian'    => 'required|integer|min:1',
    ];

    public function loadData($id)
    {
        $t = TransaksiPembelian::findOrFail($id);

        $this->transaksiId      = $t->id;
        $this->id_barang        = $t->id_barang;
        $this->id_supplier      = $t->id_supplier;
        $this->tanggal_transaksi= $t->tanggal_transaksi;
        $this->jumlah_pembelian = $t->jumlah_pembelian;
        $this->total            = $t->total;

        if ($b = Barang::find($this->id_barang)) {
            $this->harga_beli = $b->harga_beli;
        }

        $this->open = true;
    }

    public function updatedIdBarang()
    {
        if ($b = Barang::find($this->id_barang)) {
            $this->harga_beli = $b->harga_beli;
            $this->recalculate();
        }
    }

    public function updatedJumlahPembelian()
    {
        $this->recalculate();
    }

    private function recalculate()
    {
        $this->total = round($this->harga_beli * $this->jumlah_pembelian, 2);
    }

    public function update()
    {
        $this->validate();

        TransaksiPembelian::where('id', $this->transaksiId)->update([
            'id_barang'         => $this->id_barang,
            'id_supplier'       => $this->id_supplier,
            'tanggal_transaksi' => $this->tanggal_transaksi,
            'jumlah_pembelian'  => $this->jumlah_pembelian,
            'total'             => $this->total,
        ]);

        $this->reset(['transaksiId','id_barang','id_supplier','tanggal_transaksi','jumlah_pembelian','total','harga_beli']);
        $this->dispatch('refreshDatatable');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.transaksi-pembelian.update', [
            'listBarang'   => Barang::all(),
            'listSupplier' => Supplier::all(),
        ]);
    }
}
