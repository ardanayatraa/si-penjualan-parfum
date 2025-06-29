<?php

namespace App\Livewire\Akun;

use Livewire\Component;
use App\Models\Akun;

class Create extends Component
{
    public $open = false;
    public $kode_akun, $nama_akun, $tipe_akun, $parent_id;

    protected $rules = [
        'kode_akun'  => 'required|string|max:50',
        'nama_akun'  => 'required|string|max:255',
        'tipe_akun'  => 'required|string|max:50',
        'parent_id'  => 'nullable|exists:akun,id',
    ];

    public function store()
    {
        $this->validate();

        Akun::create([
            'kode_akun'  => $this->kode_akun,
            'nama_akun'  => $this->nama_akun,
            'tipe_akun'  => $this->tipe_akun,
            'parent_id'  => $this->parent_id,
        ]);

        $this->reset(['kode_akun', 'nama_akun', 'tipe_akun', 'parent_id']);
        $this->dispatch('refreshDatatable');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.akun.create', [
            'listParent' => Akun::all(),
        ]);
    }
}
