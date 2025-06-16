<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Models\User;

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
        User::where('id', $this->id)->delete();

        $this->reset();
        $this->dispatch('refreshDatatable');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.user.delete');
    }
}
