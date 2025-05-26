<?php

namespace App\Livewire\Barang;

use Livewire\Component;
use App\Models\Barang;

class Update extends Component
{
    public $open = false;
    public $id_barang;
    public $nama_barang, $harga_beli, $harga_jual, $stok;

    protected $listeners = ['edit' => 'loadData'];

    protected $rules = [
        'nama_barang' => 'required|string|max:255',
        'harga_beli'  => 'required|numeric',
        'harga_jual'  => 'required|numeric',
        'stok'        => 'required|integer',
    ];

    public function loadData($id)
    {
        $b = Barang::findOrFail($id);
        $this->id_barang   = $b->id;
        $this->nama_barang = $b->nama_barang;
        $this->harga_beli  = $b->harga_beli;
        $this->harga_jual  = $b->harga_jual;
        $this->stok        = $b->stok;
        $this->open        = true;
    }

    public function update()
    {
        $this->validate();

        Barang::where('id', $this->id_barang)->update([
            'nama_barang' => $this->nama_barang,
            'harga_beli'  => $this->harga_beli,
            'harga_jual'  => $this->harga_jual,
            'stok'        => $this->stok,
        ]);

        $this->reset(['id_barang','nama_barang','harga_beli','harga_jual','stok']);
        $this->dispatch('refreshDatatable');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.barang.update');
    }
}
