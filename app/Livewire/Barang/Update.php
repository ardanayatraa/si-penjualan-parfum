<?php

namespace App\Livewire\Barang;

use Livewire\Component;
use App\Models\Barang;
use App\Models\Supplier;

class Update extends Component
{
    public $open = false;
    public $id_barang;
    public $id_supplier;
    public $nama_barang, $satuan, $harga_beli, $harga_jual, $stok;

    protected $listeners = ['editBarang' => 'loadData'];

    protected $rules = [
        'id_supplier' => 'required|exists:supplier,id_supplier',
        'nama_barang' => 'required|string|max:255',
        'satuan'      => 'required|string|max:20',
        'harga_beli'  => 'required|numeric',
        'harga_jual'  => 'required|numeric',
        'stok'        => 'required|integer',
    ];

    public function loadData($id)
    {
        $b = Barang::findOrFail($id);
        $this->id_barang    = $b->id;
        $this->id_supplier  = $b->id_supplier;
        $this->nama_barang  = $b->nama_barang;
        $this->satuan       = $b->satuan;
        $this->harga_beli   = $b->harga_beli;
        $this->harga_jual   = $b->harga_jual;
        $this->stok         = $b->stok;
        $this->open         = true;
    }

    public function update()
    {
        $this->validate();

        Barang::where('id', $this->id_barang)->update([
            'id_supplier'  => $this->id_supplier,
            'nama_barang'  => $this->nama_barang,
            'satuan'       => $this->satuan,
            'harga_beli'   => $this->harga_beli,
            'harga_jual'   => $this->harga_jual,
            'stok'         => $this->stok,
        ]);

        $this->reset(['id_barang', 'id_supplier', 'nama_barang', 'satuan', 'harga_beli', 'harga_jual', 'stok']);
        $this->dispatch('refreshDatatable');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.barang.update', [
            'listSupplier' => Supplier::all(),
        ]);
    }
}
