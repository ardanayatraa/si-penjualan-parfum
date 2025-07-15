<?php

namespace App\Livewire\TransaksiPembelian;

use Livewire\Component;
use App\Models\TransaksiPembelian;
use App\Models\JurnalUmum;
use Illuminate\Support\Facades\DB;

class Delete extends Component
{
    public $open = false;
    public $id;

    protected $listeners = [
        'delete' => 'confirmDelete',
    ];

    public function confirmDelete($id)
    {
        $this->id = $id;
        $this->open = true;
    }

    public function delete()
    {
        DB::transaction(function() {
            // ambil transaksi
            $trans = TransaksiPembelian::findOrFail($this->id);

            // rollback stok hanya jika status selesai
            if ($trans->status === 'selesai' && $trans->barang) {
                $trans->barang->decrement('stok', $trans->jumlah_pembelian);
            }

            // hapus jurnal terkait berdasarkan keterangan
            // Pattern keterangan: "Pembelian {nama_barang} - Transaksi #{id}"
            $keteranganPattern = "- Transaksi #{$trans->id}";
            JurnalUmum::where('keterangan', 'LIKE', '%' . $keteranganPattern . '%')->delete();

            // hapus transaksi
            $trans->delete();
        });

        // reset & refresh
        $this->reset('id');
        $this->dispatch('refreshDatatable');
        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Transaksi pembelian berhasil dihapus!'
        ]);
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.transaksi-pembelian.delete');
    }
}
