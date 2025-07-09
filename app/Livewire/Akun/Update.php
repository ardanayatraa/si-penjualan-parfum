<?php

namespace App\Livewire\Akun;

use Livewire\Component;
use App\Models\Akun;

class Update extends Component
{
    public $open = false;
    public $id_akun;
    public $kode_akun, $nama_akun, $tipe_akun, $kategori_akun, $saldo_awal;

    protected $listeners = ['editAkun' => 'loadData'];

    protected $rules = [
        'kode_akun'     => 'required|string|max:10',
        'nama_akun'     => 'required|string|max:50',
        'tipe_akun'     => 'required|string|max:50',
        'kategori_akun' => 'nullable|string|max:50',
        'saldo_awal'    => 'nullable|numeric|min:0',
    ];

    public function loadData($id)
    {
        $akun = Akun::findOrFail($id);

        $this->id_akun       = $akun->id_akun;
        $this->kode_akun     = $akun->kode_akun;
        $this->nama_akun     = $akun->nama_akun;
        $this->tipe_akun     = $akun->tipe_akun;
        $this->kategori_akun = $akun->kategori_akun;
        $this->saldo_awal    = $akun->saldo_awal;
        $this->open          = true;
    }

    public function update()
    {
        $this->validate();

        Akun::where('id_akun', $this->id_akun)->update([
            'kode_akun'     => $this->kode_akun,
            'nama_akun'     => $this->nama_akun,
            'tipe_akun'     => $this->tipe_akun,
            'kategori_akun' => $this->kategori_akun,
            'saldo_awal'    => $this->saldo_awal ?? 0,
        ]);

        $this->reset(['id_akun', 'kode_akun', 'nama_akun', 'tipe_akun', 'kategori_akun', 'saldo_awal']);
        $this->dispatch('refreshDatatable');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.akun.update');
    }
}
