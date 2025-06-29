<?php

namespace App\Livewire\Akun;

use Livewire\Component;
use App\Models\Akun;

class Update extends Component
{
    public $open = false;
    public $id_akun;
    public $kode_akun, $nama_akun, $tipe_akun, $parent_id;

    protected $listeners = ['edit' => 'loadData'];

    protected $rules = [
        'kode_akun'  => 'required|string|max:50',
        'nama_akun'  => 'required|string|max:255',
        'tipe_akun'  => 'required|string|max:50',
        'parent_id'  => 'nullable|exists:akun,id',
    ];

    public function loadData($id)
    {
        $akun = Akun::findOrFail($id);
        $this->id_akun   = $akun->id;
        $this->kode_akun = $akun->kode_akun;
        $this->nama_akun = $akun->nama_akun;
        $this->tipe_akun = $akun->tipe_akun;
        $this->parent_id = $akun->parent_id;
        $this->open      = true;
    }

    public function update()
    {
        $this->validate();

        Akun::where('id', $this->id_akun)->update([
            'kode_akun' => $this->kode_akun,
            'nama_akun' => $this->nama_akun,
            'tipe_akun' => $this->tipe_akun,
            'parent_id' => $this->parent_id,
        ]);

        $this->reset(['id_akun','kode_akun','nama_akun','tipe_akun','parent_id']);
        $this->dispatch('refreshDatatable');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.akun.update', [
            'listParent' => Akun::all(),
        ]);
    }
}
