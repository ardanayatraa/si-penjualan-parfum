<?php

namespace App\Livewire\JurnalUmum;

use Livewire\Component;
use App\Models\JurnalUmum;
use App\Models\Akun;

class Create extends Component
{
    public $open = false;
    public $tanggal, $id_akun, $debit, $kredit, $keterangan;

    protected function rules()
    {
        return [
            'tanggal'    => 'required|date',
            'id_akun'    => 'required|exists:akun,id_akun',
            'debit'      => 'nullable|numeric|min:0',
            'kredit'     => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string|max:100',
        ];
    }

    public function store()
    {
        $this->validate();

        JurnalUmum::create([
            'tanggal'    => $this->tanggal,
            'id_akun'    => $this->id_akun,
            'debit'      => $this->debit ?? 0,
            'kredit'     => $this->kredit ?? 0,
            'keterangan' => $this->keterangan,
        ]);

        $this->reset(['tanggal', 'id_akun', 'debit', 'kredit', 'keterangan']);
        $this->dispatch('refreshDatatable');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.jurnal-umum.create', [
            'listAkun' => Akun::all(),
        ]);
    }
}
