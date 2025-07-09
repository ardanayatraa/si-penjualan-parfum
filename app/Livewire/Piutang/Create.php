<?php

namespace App\Livewire\Piutang;

use Livewire\Component;
use App\Models\Piutang;
use App\Models\TransaksiPenjualan;

class Create extends Component
{
    public $open = false;
    public $id_penjualan, $nama_pelanggan, $no_telp, $jumlah, $status;

    protected $rules = [
        'id_penjualan'   => 'required|exists:transaksi_penjualan,id',
        'nama_pelanggan' => 'required|string|max:50',
        'no_telp'        => 'required|string|max:15',
        'jumlah'         => 'required|numeric|min:0',
        'status'         => 'required|string|max:20',
    ];

    public function store()
    {
        $this->validate();

        Piutang::create([
            'id_penjualan'   => $this->id_penjualan,
            'nama_pelanggan' => $this->nama_pelanggan,
            'no_telp'        => $this->no_telp,
            'jumlah'         => $this->jumlah,
            'status'         => $this->status,
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
