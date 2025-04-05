<?php

namespace App\Livewire\Supplier;

use Livewire\Component;
use App\Models\Supplier;

class Create extends Component
{
    public $open = false;
    public $nama_supplier, $alamat, $no_telp;

    protected $rules = [
        'nama_supplier' => 'required|string|max:255',
        'alamat' => 'nullable|string|max:255',
        'no_telp' => 'nullable|string|max:20',
    ];

    public function store()
    {
        $this->validate();

        Supplier::create([
            'nama_supplier' => $this->nama_supplier,
            'alamat' => $this->alamat,
            'no_telp' => $this->no_telp,
        ]);

        $this->reset();
        $this->dispatch('refreshDatatable');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.supplier.create');
    }
}
