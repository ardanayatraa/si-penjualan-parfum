<?php

namespace App\Livewire\Supplier;

use Livewire\Component;
use App\Models\Supplier;
use App\Models\Barang;

class Create extends Component
{
    public $open = false;
    public $nama_supplier;
    public $alamat;
    public $no_telp;
    public $id_barang = []; // tampung pilihan barang

    protected $rules = [
        'nama_supplier' => 'required|string|max:255',
        'alamat'        => 'nullable|string|max:255',
        'no_telp'       => 'nullable|string|max:20',
        'id_barang'     => 'nullable|array',
        'id_barang.*'   => 'exists:barang,id',
    ];

    public function store()
    {
        $this->validate();

        Supplier::create([
            'nama_supplier' => $this->nama_supplier,
            'alamat'        => $this->alamat,
            'no_telp'       => $this->no_telp,
            'id_barang'     => $this->id_barang, // akan di–cast ke JSON
        ]);

        $this->reset(['nama_supplier','alamat','no_telp','id_barang','open']);
        $this->dispatch('refreshDatatable');
    }

    public function render()
    {
        return view('livewire.supplier.create', [
            'barangs' => Barang::orderBy('nama_barang')->get(),
        ]);
    }
}
