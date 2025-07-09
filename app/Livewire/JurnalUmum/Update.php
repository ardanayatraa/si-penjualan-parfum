<?php

namespace App\Livewire\JurnalUmum;

use Livewire\Component;
use App\Models\JurnalUmum;
use App\Models\Akun;

class Update extends Component
{
    public $open = false;
    public $jurnal_id;
    public $tanggal, $id_akun, $debit, $kredit, $keterangan;

    protected $listeners = ['editJurnal' => 'loadData'];

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

    public function loadData($id)
    {
        $j = JurnalUmum::findOrFail($id);
        $this->jurnal_id  = $j->id_jurnal;
        $this->tanggal    = $j->tanggal;
        $this->id_akun    = $j->id_akun;
        $this->debit      = $j->debit;
        $this->kredit     = $j->kredit;
        $this->keterangan = $j->keterangan;
        $this->open       = true;
    }

    public function update()
    {
        $this->validate();

        JurnalUmum::where('id_jurnal', $this->jurnal_id)->update([
            'tanggal'    => $this->tanggal,
            'id_akun'    => $this->id_akun,
            'debit'      => $this->debit ?? 0,
            'kredit'     => $this->kredit ?? 0,
            'keterangan' => $this->keterangan,
        ]);

        $this->reset(['jurnal_id', 'tanggal', 'id_akun', 'debit', 'kredit', 'keterangan']);
        $this->dispatch('refreshDatatable');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.jurnal-umum.update', [
            'listAkun' => Akun::all(),
        ]);
    }
}
