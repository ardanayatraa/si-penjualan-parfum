<?php

namespace App\Livewire\Supplier;

use Livewire\Component;
use App\Models\Supplier;

class Update extends Component
{
    public $open = false;
    public $id_supplier;
    public $nama_supplier, $alamat, $no_telp;

    protected $listeners = ['edit' => 'loadData'];

    protected $rules = [
        'nama_supplier' => 'required|string|max:255',
        'alamat' => 'nullable|string',
        'no_telp' => 'nullable|string|max:20',
    ];

    public function loadData($id)
    {
        $supplier = Supplier::findOrFail($id);

        $this->id_supplier = $supplier->id_supplier;
        $this->nama_supplier = $supplier->nama_supplier;
        $this->alamat = $supplier->alamat;
        $this->no_telp = $supplier->no_telp;

        $this->open = true;
    }

    public function update()
    {
        $this->validate();

        Supplier::where('id_supplier', $this->id_supplier)->update([
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
        return view('livewire.supplier.update');
    }
}
