<?php

namespace App\Livewire\Barang;

use Livewire\Component;
use App\Models\Barang;

class Create extends Component
{
    public $open = false;
    public $nama_barang, $harga_beli, $harga_jual, $stok;

    protected $rules = [
        'nama_barang' => 'required|string|max:255',
        'harga_beli'  => 'required|numeric',
        'harga_jual'  => 'required|numeric',
        'stok'        => 'required|integer',
    ];

    public function store()
    {
        $this->validate();

        Barang::create([
            'nama_barang' => $this->nama_barang,
            'harga_beli'  => $this->harga_beli,
            'harga_jual'  => $this->harga_jual,
            'stok'        => $this->stok,
        ]);

        $this->reset(['nama_barang','harga_beli','harga_jual','stok']);
        $this->dispatch('refreshDatatable');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.barang.create');
    }
}
