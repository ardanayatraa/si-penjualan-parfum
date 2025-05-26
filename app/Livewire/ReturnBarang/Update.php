<?php

namespace App\Livewire\ReturnBarang;

use Livewire\Component;
use App\Models\ReturnBarang;
use App\Models\Barang;
use App\Models\Supplier;

class Update extends Component
{
    public $open = false;
    public $returnId;
    public $id_barang, $id_supplier, $jumlah, $alasan, $tanggal_return;

    protected $listeners = ['editReturn' => 'loadData'];

    protected $rules = [
        'id_barang'       => 'required|exists:barang,id',
        'id_supplier'     => 'required|exists:suppliers,id',
        'jumlah'          => 'required|integer|min:1',
        'alasan'          => 'required|string|max:500',
        'tanggal_return'  => 'required|date',
    ];

    public function loadData($id)
    {
        $r = ReturnBarang::findOrFail($id);

        $this->returnId        = $r->id;
        $this->id_barang       = $r->id_barang;
        $this->id_supplier     = $r->id_supplier;
        $this->jumlah          = $r->jumlah;
        $this->alasan          = $r->alasan;
        $this->tanggal_return  = $r->tanggal_return;
        $this->open            = true;
    }

    public function update()
    {
        $this->validate();

        ReturnBarang::where('id', $this->returnId)->update([
            'id_barang'      => $this->id_barang,
            'id_supplier'    => $this->id_supplier,
            'jumlah'         => $this->jumlah,
            'alasan'         => $this->alasan,
            'tanggal_return' => $this->tanggal_return,
        ]);

        $this->reset(['returnId','id_barang','id_supplier','jumlah','alasan','tanggal_return']);
        $this->dispatch('refreshDatatable');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.return-barang.update', [
            'listBarang'   => Barang::all(),
            'listSupplier' => Supplier::all(),
        ]);
    }
}
