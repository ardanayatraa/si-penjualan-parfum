<?php

namespace App\Livewire\Pengaturan;

use Livewire\Component;
use App\Models\Pengaturan;

class Update extends Component
{
    public $open = false;
    public $pk;
    public $nilai_pengaturan;
    public $keterangan;

    protected $listeners = ['editPengaturan' => 'loadData'];

    protected $rules = [
        'nilai_pengaturan' => 'required|numeric',
        'keterangan'       => 'nullable|string',
    ];

    public function loadData($nama_pengaturan)
    {
        $p = Pengaturan::findOrFail($nama_pengaturan);
        $this->pk               = $p->nama_pengaturan;
        $this->nilai_pengaturan = $p->nilai_pengaturan;
        $this->keterangan       = $p->keterangan;
        $this->open             = true;
    }

    public function update()
    {
        $this->validate();

        Pengaturan::where('nama_pengaturan', $this->pk)
            ->update([
                'nilai_pengaturan' => $this->nilai_pengaturan,
                'keterangan'       => $this->keterangan,
            ]);

        $this->reset(['pk', 'nilai_pengaturan', 'keterangan']);
        $this->dispatch('refreshPengaturanList');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.pengaturan.update');
    }
}
