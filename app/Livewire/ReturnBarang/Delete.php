<?php

namespace App\Livewire\ReturnBarang;

use Livewire\Component;
use App\Models\ReturnBarang;
use App\Models\Barang;
use App\Models\JurnalUmum;
use App\Models\DetailJurnal;

class Delete extends Component
{
    public $open = false;
    public $returnId;

    protected $listeners = [
        'delete' => 'confirmDelete'
    ];

    public function confirmDelete($id)
    {
        $this->returnId = $id;
        $this->open     = true;
    }

    public function delete()
    {
        $r = ReturnBarang::findOrFail($this->returnId);

        // 1) Restore stok
        if ($barang = Barang::find($r->id_barang)) {
            $barang->increment('stok', $r->jumlah);
        }

        // 2) Hapus jurnal lama (header + detail)
        if ($r->jurnal_umum_id) {
            DetailJurnal::where('jurnal_umum_id', $r->jurnal_umum_id)->delete();
            JurnalUmum::where('id', $r->jurnal_umum_id)->delete();
        }

        // 3) Hapus record retur
        $r->delete();

        // Reset & notify
        $this->reset('returnId');
        $this->dispatch('refreshDatatable');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.return-barang.delete');
    }
}
