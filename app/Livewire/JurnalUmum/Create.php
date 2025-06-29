<?php

namespace App\Livewire\JurnalUmum;

use Livewire\Component;
use App\Models\JurnalUmum;
use App\Models\Akun;
use App\Models\DetailJurnal;
use Illuminate\Support\Facades\DB;

class Create extends Component
{
    public $open = false;
    public $tanggal, $no_bukti, $keterangan;
    public $details = []; // each item: ['akun_id'=>null, 'debit'=>0, 'kredit'=>0]

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

    public function mount()
    {
        $this->addDetail();
    }

    public function addDetail()
    {
        $this->details[] = ['akun_id' => null, 'debit' => 0, 'kredit' => 0];
    }

    public function removeDetail($index)
    {
        unset($this->details[$index]);
        $this->details = array_values($this->details);
    }

    public function store()
    {
        $this->validate();

        DB::transaction(function() {
            $j = JurnalUmum::create([
                'tanggal'    => $this->tanggal,
                'no_bukti'   => $this->no_bukti,
                'keterangan' => $this->keterangan,
            ]);

            foreach ($this->details as $row) {
                DetailJurnal::create([
                    'jurnal_umum_id' => $j->id,
                    'akun_id'        => $row['akun_id'],
                    'debit'          => $row['debit'],
                    'kredit'         => $row['kredit'],
                ]);
            }
        });

        // reset form
        $this->reset(['tanggal','no_bukti','keterangan','details']);
        $this->mount(); // initialize one detail row
        $this->dispatch('refreshDatatable');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.jurnal-umum.create', [
            'listAkun' => Akun::all()
        ]);
    }
}
