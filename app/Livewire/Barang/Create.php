<?php

namespace App\Livewire\Barang;

use Livewire\Component;
use App\Models\Barang;
use App\Models\Supplier;

class Create extends Component
{
    public $open = false;
    public $id_supplier, $nama_barang, $satuan, $harga_beli, $harga_jual, $stok;

    protected $rules = [
        'id_supplier'  => 'required|exists:supplier,id_supplier',
        'nama_barang'  => 'required|string|max:255',
        'satuan'       => 'required|string|max:20',
        'harga_beli'   => 'required|numeric',
        'harga_jual'   => 'required|numeric',
        'stok'         => 'required|integer',
    ];

    public function store()
    {
        $this->validate();

        Barang::create([
            'id_supplier'  => $this->id_supplier,
            'nama_barang'  => $this->nama_barang,
            'satuan'       => $this->satuan,
            'harga_beli'   => $this->harga_beli,
            'harga_jual'   => $this->harga_jual,
            'stok'         => $this->stok,
        ]);

        $this->reset(['id_supplier', 'nama_barang', 'satuan', 'harga_beli', 'harga_jual', 'stok']);
        $this->dispatch('refreshDatatable');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.barang.create', [
            'listSupplier' => Supplier::all(),
        ]);
    }
}
