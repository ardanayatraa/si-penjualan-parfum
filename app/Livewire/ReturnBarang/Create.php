<?php

namespace App\Livewire\ReturnBarang;

use Livewire\Component;
use App\Models\ReturnBarang;
use App\Models\Barang;
use App\Models\Supplier;

class Create extends Component
{
    public $open = false;
    public $id_barang, $id_supplier, $jumlah, $alasan, $tanggal_return;

    protected $rules = [
        'id_barang'       => 'required|exists:barang,id',
        'id_supplier'     => 'required|exists:suppliers,id',
        'jumlah'          => 'required|integer|min:1',
        'alasan'          => 'required|string|max:500',
        'tanggal_return'  => 'required|date',
    ];

    public function store()
    {
        $this->validate();

        ReturnBarang::create([
            'id_barang'      => $this->id_barang,
            'id_supplier'    => $this->id_supplier,
            'jumlah'         => $this->jumlah,
            'alasan'         => $this->alasan,
            'tanggal_return' => $this->tanggal_return,
        ]);

        $this->reset(['id_barang','id_supplier','jumlah','alasan','tanggal_return']);
        $this->dispatch('refreshDatatable');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.return-barang.create', [
            'listBarang'   => Barang::all(),
            'listSupplier' => Supplier::all(),
        ]);
    }
}
