<?php

namespace App\Livewire\Barang;

use Livewire\Component;
use App\Models\Barang;

class Delete extends Component
{
    public $open = false;
    public $id_barang;

    protected $listeners = ['delete' => 'confirmDelete'];

    public function confirmDelete($id)
    {
        $this->id_barang = $id;
        $this->open = true;
    }

    public function delete()
    {
        Barang::where('id', $this->id_barang)->delete();

        $this->reset();
        $this->dispatch('refreshDatatable');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.barang.delete');
    }
}
