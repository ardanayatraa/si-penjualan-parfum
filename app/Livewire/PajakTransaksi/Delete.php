<?php

namespace App\Livewire\PajakTransaksi;

use Livewire\Component;
use App\Models\PajakTransaksi;

class Delete extends Component
{
    public $open = false;
    public $id;

    protected $listeners = ['delete' => 'confirmDelete'];

    public function confirmDelete($id)
    {
        $this->id = $id;
        $this->open = true;
    }

    public function delete()
    {
        PajakTransaksi::where('id', $this->id)->delete();

        $this->reset();
        $this->dispatch('refreshDatatable');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.pajak-transaksi.delete');
    }
}
