<?php

namespace App\Livewire\Supplier;

use Livewire\Component;
use App\Models\Supplier;
use App\Models\Barang;

class Update extends Component
{
    public $open = false;
    public $id_supplier;
    public $nama_supplier;
    public $alamat;
    public $no_telp;
    public $id_barang = [];

    protected $listeners = ['edit' => 'loadData'];

    protected function rules()
    {
        return [
            'nama_supplier' => 'required|string|max:255',
            'alamat'        => 'nullable|string',
            'no_telp'       => 'nullable|string|max:20',
            'id_barang'     => 'nullable|array',
            'id_barang.*'   => 'exists:barang,id',
        ];
    }

    public function loadData($id)
    {
        $supplier = Supplier::findOrFail($id);

        $this->id_supplier   = $supplier->id_supplier;
        $this->nama_supplier = $supplier->nama_supplier;
        $this->alamat        = $supplier->alamat;
        $this->no_telp       = $supplier->no_telp;
        $this->id_barang     = $supplier->id_barang ?? [];

        $this->open = true;
    }

    public function update()
    {
        $this->validate();

        Supplier::where('id_supplier', $this->id_supplier)->update([
            'nama_supplier' => $this->nama_supplier,
            'alamat'        => $this->alamat,
            'no_telp'       => $this->no_telp,
            'id_barang'     => $this->id_barang,
        ]);

        $this->reset(['id_supplier','nama_supplier','alamat','no_telp','id_barang','open']);
        $this->dispatch('refreshDatatable');
    }

    public function render()
    {
        return view('livewire.supplier.update', [
            'barangs' => Barang::orderBy('nama_barang')->get(),
        ]);
    }
}
