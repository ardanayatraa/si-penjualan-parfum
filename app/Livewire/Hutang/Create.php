<?php

namespace App\Livewire\Hutang;

use Livewire\Component;
use App\Models\Hutang;

class Create extends Component
{
    public $open = false;
    public $id_pembelian, $id_supplier, $jumlah, $jumlah_dibayarkan, $tgl_tempo, $status;

    protected $rules = [
        'id_pembelian' => 'required|exists:transaksi_pembelian,id',
        'id_supplier'  => 'required|exists:supplier,id_supplier',
        'jumlah'       => 'required|numeric|min:0',
        'jumlah_dibayarkan' => 'required|numeric|min:0',
        'tgl_tempo'    => 'required|date',
        'status'       => 'required|string|max:20',
    ];

    public function store()
    {
        $this->validate();

        Hutang::create([
            'id_pembelian' => $this->id_pembelian,
            'id_supplier'  => $this->id_supplier,
            'jumlah'       => $this->jumlah,
            'jumlah_dibayarkan' => $this->jumlah_dibayarkan,
            'tgl_tempo'    => $this->tgl_tempo,
            'status'       => $this->status,
        ]);

        $this->reset(['id_pembelian', 'id_supplier', 'jumlah', 'jumlah_dibayarkan', 'tgl_tempo', 'status']);
        $this->dispatch('refreshDatatable');
        $this->open = false;
    }

   public function render()
{
    return view('livewire.hutang.create', [
        'listSupplier' => \App\Models\Supplier::all(),
        'listPembelian' => \App\Models\TransaksiPembelian::all(),
    ]);
}
}
