<?php

namespace App\Livewire\PajakTransaksi;

use Livewire\Component;
use App\Models\PajakTransaksi;

class Update extends Component
{
    public $open = false;
    public $pajakId;
    public $nama, $presentase;

    protected $listeners = ['edit' => 'loadPajak'];

    protected function rules()
    {
        return [
            'nama'       => "required|string|max:50|unique:pajak_transaksi,nama,{$this->pajakId}",
            'presentase' => 'required|numeric|min:0|max:100',
        ];
    }

    public function loadPajak($id)
    {
        $p = PajakTransaksi::findOrFail($id);

        $this->pajakId    = $p->id;
        $this->nama       = $p->nama;
        $this->presentase = $p->presentase;
        $this->open       = true;
    }

    public function update()
    {
        $this->validate();

        PajakTransaksi::where('id', $this->pajakId)->update([
            'nama'       => $this->nama,
            'presentase' => $this->presentase,
        ]);

        $this->reset(['pajakId', 'nama', 'presentase']);
        $this->dispatch('refreshDatatable');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.pajak-transaksi.update');
    }
}
