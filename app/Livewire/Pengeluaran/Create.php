<?php

namespace App\Livewire\Pengeluaran;

use Livewire\Component;
use App\Models\Pengeluaran;
use App\Models\Akun;
use App\Models\User;

class Create extends Component
{
    public $open = false;
    public $id_akun, $id_user, $tanggal, $jenis_pengeluaran, $jumlah, $keterangan;

    protected $rules = [
        'id_akun' => 'required|exists:akun,id_akun',
        'id_user' => 'required|exists:users,id',
        'tanggal' => 'required|date',
        'jenis_pengeluaran' => 'required|string|max:50',
        'jumlah' => 'required|numeric',
        'keterangan' => 'nullable|string|max:100',
    ];

    public function store()
    {
        $this->validate();
        Pengeluaran::create([
            'id_akun' => $this->id_akun,
            'id_user' => $this->id_user,
            'tanggal' => $this->tanggal,
            'jenis_pengeluaran' => $this->jenis_pengeluaran,
            'jumlah' => $this->jumlah,
            'keterangan' => $this->keterangan,
        ]);
        $this->reset(['id_akun', 'id_user', 'tanggal', 'jenis_pengeluaran', 'jumlah', 'keterangan']);
        $this->dispatch('refreshDatatable');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.pengeluaran.create', [
            'listAkun' => Akun::all(),
            'listUser' => User::all(),
        ]);
    }
}
