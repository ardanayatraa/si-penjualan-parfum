<?php

namespace App\Livewire\ReturnBarang;

use Livewire\Component;
use App\Models\ReturnBarang;
use App\Models\Barang;
use App\Models\JurnalUmum;
use App\Models\TransaksiPembelian;
use Illuminate\Support\Facades\DB;

class Delete extends Component
{
    public $open = false;
    public $returnId;
    public $returnData = null;

    protected $listeners = [
        'delete' => 'confirmDelete'
    ];

    public function confirmDelete($id)
    {
        $this->returnId = $id;

        // Load data return untuk preview di modal
        $this->returnData = ReturnBarang::with(['barang', 'supplier'])->find($id);

        $this->open = true;
    }

    public function delete()
    {
        if (!$this->returnId) {
            $this->addError('general', 'Return barang tidak ditemukan.');
            return;
        }

        $return = ReturnBarang::findOrFail($this->returnId);

        try {
            DB::transaction(function () use ($return) {
                // 1. Kembalikan stok barang
                if ($barang = Barang::find($return->id_barang)) {
                    $stokSebelum = $barang->stok;
                    $barang->increment('stok', $return->jumlah);
                    $barang->refresh();

                    \Log::info("Delete Return - Barang ID {$return->id_barang}: stok sebelum: {$stokSebelum}, setelah: {$barang->stok}, ditambah: {$return->jumlah}");
                }

                // 2. Kembalikan jumlah_pembelian di transaksi (jika ada transaksi terkait)
                // Cari transaksi pembelian berdasarkan barang dan tanggal yang mendekati
                $transaksi = TransaksiPembelian::where('id_barang', $return->id_barang)
                    ->where('id_supplier', $return->id_supplier)
                    ->whereDate('tanggal_transaksi', '<=', $return->tanggal_return)
                    ->orderBy('tanggal_transaksi', 'desc')
                    ->first();

                if ($transaksi) {
                    $jumlahSebelum = $transaksi->jumlah_pembelian;
                    $transaksi->increment('jumlah_pembelian', $return->jumlah);
                    $transaksi->refresh();

                    // Update total transaksi
                    $transaksi->update([
                        'total' => $transaksi->jumlah_pembelian * $transaksi->harga
                    ]);

                    \Log::info("Delete Return - Transaksi ID {$transaksi->id}: jumlah_pembelian sebelum: {$jumlahSebelum}, setelah: {$transaksi->jumlah_pembelian}");
                }

                // 3. Hapus jurnal yang terkait dengan return ini
                $this->hapusJurnalReturn($return);

                // 4. Hapus record return barang
                $return->delete();
            });

            $this->resetForm();
            $this->dispatch('refreshDatatable');
            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Return barang berhasil dihapus dan stok dikembalikan!'
            ]);
            $this->open = false;

        } catch (\Exception $e) {
            \Log::error("Error deleting return: " . $e->getMessage());
            $this->addError('general', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    protected function hapusJurnalReturn($return)
    {
        // Hapus jurnal berdasarkan keterangan yang mengandung Return ID
        $deletedCount = JurnalUmum::where('keterangan', 'LIKE', "%Return ID {$return->id}%")
                                 ->orWhere('keterangan', 'LIKE', "%Update Return barang%Return ID {$return->id}%")
                                 ->delete();

        \Log::info("Deleted {$deletedCount} jurnal entries for Return ID {$return->id}");

        // Jika ada jurnal_umum_id di return barang (backup method)
        if (isset($return->jurnal_umum_id) && $return->jurnal_umum_id) {
            JurnalUmum::where('id', $return->jurnal_umum_id)->delete();
        }
    }

    private function resetForm()
    {
        $this->reset([
            'returnId',
            'returnData',
        ]);
        $this->resetErrorBag();
    }

    public function closeModal()
    {
        $this->resetForm();
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.return-barang.delete', [
            'returnData' => $this->returnData
        ]);
    }
}
