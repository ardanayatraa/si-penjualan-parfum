<?php

namespace App\Livewire\Supplier;

use Livewire\Component;
use App\Models\Supplier;

class Delete extends Component
{
    public $open = false;
    public $id_supplier;

    protected $listeners = ['delete' => 'confirmDelete'];

    public function confirmDelete($id)
    {
        $this->id_supplier = $id;
        $this->open = true;
    }

    public function delete()
    {
        Supplier::where('id_supplier', $this->id_supplier)->delete();

        $this->reset();
        $this->dispatch('refreshDatatable');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.supplier.delete');
    }
}
