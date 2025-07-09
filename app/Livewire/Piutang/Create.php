<?php

namespace App\Livewire\Piutang;

use Livewire\Component;
use App\Models\Piutang;
use App\Models\TransaksiPenjualan;

class Create extends Component
{
    public $open = false;
    public $id_penjualan, $jumlah, $status;

    protected $rules = [
        'id_penjualan' => 'required|exists:transaksi_penjualan,id',
        'jumlah'       => 'required|numeric|min:0',
        'status'       => 'required|string|max:20',
    ];

    public function store()
    {
        $this->validate();

        Piutang::create([
            'id_penjualan' => $this->id_penjualan,
            'jumlah'       => $this->jumlah,
            'status'       => $this->status,
        ]);

        $this->reset();
        $this->dispatch('refreshDatatable');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.piutang.create', [
            'listPenjualan' => TransaksiPenjualan::all(),
        ]);
    }
}
