<?php

namespace App\Livewire\TransaksiPembelian;

use Livewire\Component;
use App\Models\TransaksiPembelian;
use App\Models\Barang;
use App\Models\Supplier;

class Create extends Component
{
    public $open = false;
    public $id_barang, $id_supplier, $tanggal_transaksi, $jumlah_pembelian = 1, $total = 0;
    public $harga_beli = 0;

    protected $rules = [
        'id_barang'           => 'required|exists:barang,id',
        'id_supplier'         => 'required|exists:supplier,id_supplier',
        'tanggal_transaksi'   => 'required|date',
        'jumlah_pembelian'    => 'required|integer|min:1',
        // total dihitung otomatis
    ];


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

    public function store()
    {
        $this->validate();

        TransaksiPembelian::create([
            'id_barang'         => $this->id_barang,
            'id_supplier'       => $this->id_supplier,
            'tanggal_transaksi' => $this->tanggal_transaksi,
            'jumlah_pembelian'  => $this->jumlah_pembelian,
            'total'             => $this->total,
        ]);

        $this->reset(['id_barang','id_supplier','tanggal_transaksi','jumlah_pembelian','total','harga_beli']);
        $this->dispatch('refreshDatatable');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.transaksi-pembelian.create', [
            'listBarang'   => Barang::all(),
            'listSupplier' => Supplier::all(),
        ]);
    }
}
