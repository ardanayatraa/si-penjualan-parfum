<?php

namespace App\Livewire\Pengeluaran;

use Livewire\Component;
use App\Models\Pengeluaran;
use App\Models\Akun;
use App\Models\User;

class Update extends Component
{
    public $open = false;
    public $pengeluaran_id;
    public $id_akun, $id_user, $tanggal, $jenis_pengeluaran, $jumlah, $keterangan;

    protected $listeners = ['editPengeluaran' => 'edit'];

    protected $rules = [
        'id_akun' => 'required|exists:akun,id_akun',
        'id_user' => 'required|exists:users,id',
        'tanggal' => 'required|date',
        'jenis_pengeluaran' => 'required|string|max:50',
        'jumlah' => 'required|numeric',
        'keterangan' => 'nullable|string|max:100',
    ];

    public function edit($id)
    {
        $pengeluaran = Pengeluaran::findOrFail($id);
        $this->pengeluaran_id = $pengeluaran->id_pengeluaran; 
        $this->id_akun = $pengeluaran->id_akun;
        $this->id_user = $pengeluaran->id_user;
           $this->tanggal = $pengeluaran->tanggal->format('Y-m-d'); // Pastikan format Y-m-d

        $this->jenis_pengeluaran = $pengeluaran->jenis_pengeluaran;
        $this->jumlah = $pengeluaran->jumlah;
        $this->keterangan = $pengeluaran->keterangan;
        $this->open = true;
    }

    public function update()
    {
        $this->validate();
        Pengeluaran::where('id_pengeluaran', $this->pengeluaran_id)
            ->update([
                'id_akun' => $this->id_akun,
                'id_user' => $this->id_user,
                'tanggal' => $this->tanggal,
                'jenis_pengeluaran' => $this->jenis_pengeluaran,
                'jumlah' => $this->jumlah,
                'keterangan' => $this->keterangan,
            ]);
        $this->dispatch('refreshDatatable');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.pengeluaran.update', [
            'listAkun' => Akun::all(),
            'listUser' => User::all(),
        ]);
    }
}
