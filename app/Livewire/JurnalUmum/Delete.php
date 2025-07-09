<?php

namespace App\Livewire\JurnalUmum;

use Livewire\Component;
use App\Models\JurnalUmum;

class Delete extends Component
{
    public $open = false;
    public $jurnal_id;

    protected $listeners = ['deleteJurnal' => 'confirmDelete'];

    public function confirmDelete($id)
    {
        $this->jurnal_id = $id;
        $this->open = true;
    }

    public function delete()
    {
        JurnalUmum::where('id_jurnal', $this->jurnal_id)->delete();
        $this->reset();
        $this->dispatch('refreshDatatable');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.jurnal-umum.delete');
    }
}
