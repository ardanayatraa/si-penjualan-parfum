<?php

namespace App\Livewire\JurnalUmum;

use Livewire\Component;
use App\Models\JurnalUmum;
use App\Models\Akun;
use App\Models\DetailJurnal;
use Illuminate\Support\Facades\DB;

class Update extends Component
{
    public $open = false;
    public $jurnal_id;
    public $tanggal, $no_bukti, $keterangan;
    public $details = [];

    protected $listeners = ['edit' => 'loadData'];

    protected function rules()
    {
        return [
            'tanggal'              => 'required|date',
            'no_bukti'             => 'required|string|max:100',
            'keterangan'           => 'nullable|string|max:255',
            'details.*.akun_id'    => 'required|exists:akun,id',
            'details.*.debit'      => 'required|numeric|min:0',
            'details.*.kredit'     => 'required|numeric|min:0',
        ];
    }

    public function loadData($id)
    {
        $j = JurnalUmum::with('detailJurnal')->findOrFail($id);
        $this->jurnal_id  = $j->id;
        $this->tanggal    = $j->tanggal->format('Y-m-d');
        $this->no_bukti   = $j->no_bukti;
        $this->keterangan = $j->keterangan;
        $this->details    = $j->detailJurnal->map(fn($d) => [
            'akun_id' => $d->akun_id,
            'debit'   => $d->debit,
            'kredit'  => $d->kredit,
        ])->toArray();
        $this->open = true;
    }

    public function addDetail()
    {
        $this->details[] = ['akun_id' => null, 'debit' => 0, 'kredit' => 0];
    }

    public function removeDetail($i)
    {
        unset($this->details[$i]);
        $this->details = array_values($this->details);
    }

    public function update()
    {
        $this->validate();

        DB::transaction(function() {
            JurnalUmum::where('id', $this->jurnal_id)->update([
                'tanggal'    => $this->tanggal,
                'no_bukti'   => $this->no_bukti,
                'keterangan' => $this->keterangan,
            ]);
            DetailJurnal::where('jurnal_umum_id', $this->jurnal_id)->delete();
            foreach ($this->details as $row) {
                DetailJurnal::create([
                    'jurnal_umum_id' => $this->jurnal_id,
                    'akun_id'        => $row['akun_id'],
                    'debit'          => $row['debit'],
                    'kredit'         => $row['kredit'],
                ]);
            }
        });

        $this->reset(['tanggal','no_bukti','keterangan','details','jurnal_id']);
        $this->open = false;
        $this->dispatch('refreshDatatable');
    }

    public function render()
    {
        return view('livewire.jurnal-umum.update', [
            'listAkun' => Akun::all()
        ]);
    }
}
