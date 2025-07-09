<?php

namespace App\Livewire\Akun;

use Livewire\Component;
use App\Models\Akun;

class Delete extends Component
{
    public $open = false;
    public $id_akun;

    protected $listeners = ['deleteAkun' => 'confirmDelete'];

    public function confirmDelete($id)
    {
        $this->id_akun = $id;
        $this->open = true;
    }

    public function delete()
    {
        Akun::where('id_akun', $this->id_akun)->delete();

        $this->reset();
        $this->dispatch('refreshDatatable');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.akun.delete');
    }
}
