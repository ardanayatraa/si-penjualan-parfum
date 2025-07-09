<?php

namespace App\Livewire\Piutang;

use Livewire\Component;
use App\Models\Piutang;

class Delete extends Component
{
    public $open = false;
    public $piutang_id;

    protected $listeners = ['deletePiutang' => 'confirmDelete'];

    public function confirmDelete($id)
    {
        $this->piutang_id = $id;
        $this->open = true;
    }

    public function delete()
    {
        Piutang::where('id_piutang', $this->piutang_id)->delete();
        $this->reset();
        $this->dispatch('refreshDatatable');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.piutang.delete');
    }
}
