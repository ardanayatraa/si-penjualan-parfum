<?php

namespace App\Livewire\Barang;

use Livewire\Component;
use App\Models\Barang;

class Create extends Component
{
    public $open = false;
    public $nama_barang, $harga_beli, $harga_jual, $jumlah_retur, $jumlah_terjual, $jumlah_stok, $jumlah_nilai_stok, $keterangan;

    protected $rules = [
        'nama_barang' => 'required|string|max:255',
        'harga_beli' => 'required|numeric',
        'harga_jual' => 'required|numeric',
        'jumlah_retur' => 'nullable|integer',
        'jumlah_terjual' => 'nullable|integer',
        'jumlah_stok' => 'nullable|integer',
        'jumlah_nilai_stok' => 'nullable|numeric',
        'keterangan' => 'nullable|string',
    ];

    public function store()
    {
        $this->validate();

        Barang::create([
            'nama_barang' => $this->nama_barang,
            'harga_beli' => $this->harga_beli,
            'harga_jual' => $this->harga_jual,
            'jumlah_retur' => $this->jumlah_retur,
            'jumlah_terjual' => $this->jumlah_terjual,
            'jumlah_stok' => $this->jumlah_stok,
            'jumlah_nilai_stok' => $this->jumlah_nilai_stok,
            'keterangan' => $this->keterangan,
        ]);

        $this->reset();
        $this->dispatch('refreshDatatable');
        $this->open = false;
    }


    public function render()
    {
        return view('livewire.barang.create');
    }
}
