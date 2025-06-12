<?php

namespace App\Livewire\TransaksiPenjualan;

use Livewire\Component;
use App\Models\TransaksiPenjualan;
use App\Models\JurnalUmum;
use Illuminate\Support\Facades\DB;

class Delete extends Component
{
    public $open = false;
    public $id;

    // listener tidak diubah
    protected $listeners = ['delete' => 'confirmDelete'];

    public function confirmDelete($id)
    {
        $this->id   = $id;
        $this->open = true;
    }

    public function delete()
    {
        DB::transaction(function() {
            // ambil transaksi
            $trans = TransaksiPenjualan::findOrFail($this->id);

            // rollback stok
            if ($trans->barang) {
                $trans->barang->increment('stok', $trans->jumlah_penjualan);
            }

            // hapus jurnal umum + detail berdasarkan no_bukti "PNJ-{id}"
            $noBukti = 'PNJ-' . $trans->id;
            if ($j = JurnalUmum::where('no_bukti', $noBukti)->first()) {
                $j->detailJurnal()->delete();
                $j->delete();
            }

            // hapus transaksi
            $trans->delete();
        });

        $this->reset('id');
        $this->dispatch('refreshDatatable');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.transaksi-penjualan.delete');
    }
}
