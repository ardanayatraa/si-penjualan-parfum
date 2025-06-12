<?php

namespace App\Livewire\TransaksiPembelian;

use Livewire\Component;
use App\Models\TransaksiPembelian;
use App\Models\JurnalUmum;
use Illuminate\Support\Facades\DB;

class Delete extends Component
{
    public $open = false;
    public $id;  // tetap pakai properti "id"

    // listener tidak diubah
    protected $listeners = [
        'delete' => 'confirmDelete',
    ];

    public function confirmDelete($id)
    {
        $this->id   = $id;
        $this->open = true;
    }

    public function delete()
    {
        DB::transaction(function() {
            // ambil transaksi
            $trans = TransaksiPembelian::findOrFail($this->id);

            // rollback stok
            if ($trans->barang) {
                $trans->barang->decrement('stok', $trans->jumlah_pembelian);
            }

            // hapus jurnal terkait berdasarkan no_bukti "PBJ-{id}"
            $noBukti = 'PBJ-' . $trans->id;
            if ($j = JurnalUmum::where('no_bukti', $noBukti)->first()) {
                // hapus detail dulu
                $j->detailJurnal()->delete();
                // hapus header
                $j->delete();
            }

            // hapus transaksi
            $trans->delete();
        });

        // reset & refresh
        $this->reset('id');
        $this->emit('refreshDatatable');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.transaksi-pembelian.delete');
    }
}
