<?php

namespace App\Livewire\Pengaturan;

use Livewire\Component;
use App\Models\Pengaturan;

class Create extends Component
{
    public $open = false;
    public $nama_pengaturan;
    public $nilai_pengaturan;
    public $keterangan;

    protected $rules = [
        'nama_pengaturan'  => 'required|string|max:50|unique:pengaturan,nama_pengaturan',
        'nilai_pengaturan' => 'required|numeric',
        'keterangan'       => 'nullable|string',
    ];

    public function store()
    {
        $this->validate();

        Pengaturan::create([
            'nama_pengaturan'  => $this->nama_pengaturan,
            'nilai_pengaturan' => $this->nilai_pengaturan,
            'keterangan'       => $this->keterangan,
        ]);

        $this->reset(['nama_pengaturan', 'nilai_pengaturan', 'keterangan']);
        $this->dispatch('refreshPengaturanList');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.pengaturan.create');
    }
}
