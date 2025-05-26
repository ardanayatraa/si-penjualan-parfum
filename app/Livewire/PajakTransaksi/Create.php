<?php

namespace App\Livewire\PajakTransaksi;

use Livewire\Component;
use App\Models\PajakTransaksi;

class Create extends Component
{
    public $open = false;
    public $nama, $presentase;

    protected $rules = [
        'nama'       => 'required|string|max:50|unique:pajak_transaksi,nama',
        'presentase' => 'required|numeric|min:0|max:100',
    ];

    public function store()
    {
        $this->validate();

        PajakTransaksi::create([
            'nama'       => $this->nama,
            'presentase' => $this->presentase,
        ]);

        $this->reset(['nama', 'presentase']);
        $this->dispatch('refreshDatatable');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.pajak-transaksi.create');
    }
}
