<?php

namespace App\Livewire\Pengeluaran;

use Livewire\Component;
use App\Models\Pengeluaran;

class Delete extends Component
{
    public $open = false;
    public $pengeluaran_id;

    protected $listeners = ['deletePengeluaran' => 'confirmDelete'];

    public function confirmDelete($id)
    {
        $this->pengeluaran_id = $id;
        $this->open = true;
    }

    public function delete()
    {
        Pengeluaran::where('id_pengeluaran', $this->pengeluaran_id)->delete();
        $this->reset('pengeluaran_id');
        $this->dispatch('refreshDatatable');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.pengeluaran.delete');
    }
}
