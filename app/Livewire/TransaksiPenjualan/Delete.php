<?php

namespace App\Livewire\TransaksiPenjualan;

use Livewire\Component;
use App\Models\TransaksiPenjualan;
use App\Models\JurnalUmum;
use App\Models\Piutang;
use Illuminate\Support\Facades\DB;

class Delete extends Component
{
    public $open = false;
    public $id;
    public $transaksi; // Store transaction data for display

    protected $listeners = ['delete' => 'confirmDelete'];

    public function confirmDelete($id)
    {
        $this->id = $id;

        // Load transaction data untuk ditampilkan di modal konfirmasi
        $this->transaksi = TransaksiPenjualan::with(['kasir', 'barang', 'pajak'])
                                           ->findOrFail($id);

        $this->open = true;
    }

    public function delete()
    {
        try {
            DB::transaction(function() {
                $transaksi = TransaksiPenjualan::findOrFail($this->id);

                // Validasi: hanya transaksi pending yang bisa dihapus
                if ($transaksi->status === 'selesai') {
                    throw new \Exception('Transaksi yang sudah selesai tidak dapat dihapus. Silakan ubah status menjadi pending terlebih dahulu.');
                }

                // 1. Rollback stok hanya jika transaksi selesai sebelumnya
                if ($transaksi->status === 'selesai' && $transaksi->barang) {
                    $transaksi->barang->increment('stok', $transaksi->jumlah_terjual);
                }

                // 2. Hapus piutang terkait jika ada
                if ($transaksi->piutang) {
                    $transaksi->piutang->delete();
                }

                // 3. Hapus jurnal umum berdasarkan keterangan yang mengandung ID transaksi
                JurnalUmum::where('keterangan', 'LIKE', "%Transaksi #{$transaksi->id}%")
                          ->orWhere('keterangan', 'LIKE', "%Penjualan%{$transaksi->barang->nama_barang}%")
                          ->delete();

                // 4. Hapus transaksi
                $transaksi->delete();
            });

            $this->resetForm();
            $this->dispatch('refreshDatatable');
            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Transaksi penjualan berhasil dihapus!'
            ]);
            $this->open = false;

        } catch (\Exception $e) {
            $this->addError('delete', $e->getMessage());
        }
    }

    public function canDelete()
    {
        if (!$this->transaksi) {
            return false;
        }

        // Hanya transaksi pending dan dibatalkan yang bisa dihapus
        return in_array($this->transaksi->status, ['pending', 'dibatalkan']);
    }

    public function getDeleteWarningMessage()
    {
        if (!$this->transaksi) {
            return '';
        }

        $messages = [];

        if ($this->transaksi->status === 'selesai') {
            $messages[] = 'Transaksi ini sudah selesai dan telah mempengaruhi stok barang serta jurnal keuangan.';
        }

        if ($this->transaksi->piutang) {
            $messages[] = 'Transaksi ini memiliki piutang yang akan ikut terhapus.';
        }

        $jurnalCount = JurnalUmum::where('keterangan', 'LIKE', "%Transaksi #{$this->transaksi->id}%")->count();
        if ($jurnalCount > 0) {
            $messages[] = "Akan menghapus {$jurnalCount} entri jurnal terkait.";
        }

        return implode(' ', $messages);
    }

    private function resetForm()
    {
        $this->reset(['id', 'transaksi']);
        $this->resetErrorBag();
    }

    public function closeModal()
    {
        $this->resetForm();
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.transaksi-penjualan.delete', [
            'canDelete' => $this->canDelete(),
            'warningMessage' => $this->getDeleteWarningMessage(),
        ]);
    }
}
