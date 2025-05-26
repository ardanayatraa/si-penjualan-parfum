<?php

namespace App\Livewire\ReturnBarang;

use Livewire\Component;
use App\Models\ReturnBarang;

class Delete extends Component
{
    public $open = false;
    public $returnId;

    protected $listeners = [
        'deleteReturn' => 'confirmDelete'
    ];

    public function confirmDelete($id)
    {
        $this->returnId = $id;
        $this->open = true;
    }

    public function delete()
    {
        ReturnBarang::destroy($this->returnId);

        $this->reset(['returnId']);
        $this->dispatch('refreshDatatable');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.return-barang.delete');
    }
}
