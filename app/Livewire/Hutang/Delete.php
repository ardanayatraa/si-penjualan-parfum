<?php

namespace App\Livewire\Hutang;

use Livewire\Component;
use App\Models\Hutang;

class Delete extends Component
{
    public $open = false;
    public $id_hutang;

    protected $listeners = ['deleteHutang' => 'confirmDelete'];

    public function confirmDelete($id)
    {
        $this->id_hutang = $id;
        $this->open = true;
    }

    public function delete()
    {
        Hutang::where('id_hutang', $this->id_hutang)->delete();
        $this->reset(['id_hutang']);
        $this->dispatch('refreshDatatable');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.hutang.delete');
    }
}

