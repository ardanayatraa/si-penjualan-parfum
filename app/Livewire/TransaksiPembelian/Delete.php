<?php

namespace App\Livewire\TransaksiPembelian;

use Livewire\Component;
use App\Models\TransaksiPembelian;

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
        TransaksiPembelian::where('id', $this->id)->delete();

        $this->reset();
        $this->emit('refreshDatatable');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.transaksi-pembelian.delete');
    }
}
