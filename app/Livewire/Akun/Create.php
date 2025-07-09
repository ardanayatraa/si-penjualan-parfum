<?php

namespace App\Livewire\Akun;

use Livewire\Component;
use App\Models\Akun;

class Create extends Component
{
    public $open = false;
    public $kode_akun, $nama_akun, $tipe_akun, $kategori_akun, $saldo_awal;

    protected $rules = [
        'kode_akun'     => 'required|string|max:10',
        'nama_akun'     => 'required|string|max:50',
        'tipe_akun'     => 'required|string|max:50',
        'kategori_akun' => 'nullable|string|max:50',
        'saldo_awal'    => 'nullable|numeric|min:0',
    ];

    public function store()
    {
        $this->validate();

        Akun::create([
            'kode_akun'     => $this->kode_akun,
            'nama_akun'     => $this->nama_akun,
            'tipe_akun'     => $this->tipe_akun,
            'kategori_akun' => $this->kategori_akun,
            'saldo_awal'    => $this->saldo_awal ?? 0,
        ]);

        $this->reset(['kode_akun', 'nama_akun', 'tipe_akun', 'kategori_akun', 'saldo_awal']);
        $this->dispatch('refreshDatatable');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.akun.create');
    }
}
