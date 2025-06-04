<?php

namespace App\Livewire\Pengaturan;

use Livewire\Component;
use App\Models\Pengaturan;

class Delete extends Component
{
    public $open = false;
    public $pk; // nama_pengaturan

    protected $listeners = ['confirmDelete' => 'loadData'];

    public function loadData($nama_pengaturan)
    {
        $this->pk   = $nama_pengaturan;
        $this->open = true;
    }

    public function destroy()
    {
        Pengaturan::where('nama_pengaturan', $this->pk)->delete();
        $this->dispatch('refreshPengaturanList');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.pengaturan.delete');
    }
}
