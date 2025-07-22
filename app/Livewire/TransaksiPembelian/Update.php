<?php

namespace App\Livewire\TransaksiPembelian;

use Livewire\Component;
use App\Models\TransaksiPembelian;
use App\Models\Barang;
use App\Models\Supplier;
use App\Models\JurnalUmum;
use App\Models\Akun;
use Illuminate\Support\Facades\DB;

class Update extends Component
{
    public $open = false;
    public $transaksiId;

    // Form fields sesuai migration baru
    public $id_barang;
    public $id_supplier;
    public $tanggal_transaksi;
    public $jumlah_pembelian;
    public $harga; // Harga per unit
    public $total;
    public $metode_pembayaran = 'cash';
    // Hapus status karena langsung selesai

    // Original data untuk tracking perubahan
    public $originalData = [];

    // Payment method options
    public $metodePembayaranOptions = [
        'cash' => 'Tunai',
        'qris' => 'Qris',
    ];

    protected $listeners = ['edit' => 'loadData'];

    protected function rules()
    {
        return [
            'id_barang'         => 'required|exists:barang,id',
            'id_supplier'       => 'required|exists:supplier,id_supplier',
            'tanggal_transaksi' => 'required|date',
            'jumlah_pembelian'  => 'required|integer|min:1',
            'harga'             => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|in:cash,qris',
        ];
    }

    protected $messages = [
        'id_barang.required'         => 'Barang harus dipilih.',
        'id_supplier.required'       => 'Supplier harus dipilih.',
        'tanggal_transaksi.required' => 'Tanggal transaksi harus diisi.',
        'jumlah_pembelian.required'  => 'Jumlah pembelian harus diisi.',
        'jumlah_pembelian.min'       => 'Jumlah pembelian minimal 1.',
        'harga.required'             => 'Harga harus diisi.',
        'harga.min'                  => 'Harga tidak boleh negatif.',
    ];

    public function loadData($id)
    {
        $transaksi = TransaksiPembelian::findOrFail($id);

        // Store original data untuk tracking perubahan
        $this->originalData = [
            'id_barang' => $transaksi->id_barang,
            'jumlah_pembelian' => $transaksi->jumlah_pembelian,
            'status' => $transaksi->status,
        ];

        // Load data ke form
        $this->transaksiId = $transaksi->id;
        $this->id_barang = $transaksi->id_barang;
        $this->id_supplier = $transaksi->id_supplier;
        $this->tanggal_transaksi = $transaksi->tanggal_transaksi->format('Y-m-d');
        $this->jumlah_pembelian = $transaksi->jumlah_pembelian;
        $this->harga = $transaksi->harga;
        $this->total = $transaksi->total;
        $this->metode_pembayaran = $transaksi->metode_pembayaran;
        // Tidak set status karena langsung selesai

        $this->open = true;
    }

    public function updatedIdSupplier($supplierId)
    {
        // Reset barang selection when supplier changes
        $this->id_barang = '';
        $this->harga = 0;
        $this->recalculate();
    }

    public function updatedIdBarang()
    {
        if ($barang = Barang::find($this->id_barang)) {
            $this->harga = $barang->harga_beli;
            $this->recalculate();
        }
    }

    public function updatedJumlahPembelian()
    {
        $this->recalculate();
    }

    public function updatedHarga()
    {
        $this->recalculate();
    }

    private function recalculate()
    {
        if ($this->harga && $this->jumlah_pembelian) {
            $this->total = $this->harga * $this->jumlah_pembelian;
        } else {
            $this->total = 0;
        }
    }

    public function update()
    {
        $this->validate();

        try {
            DB::transaction(function() {
                $transaksi = TransaksiPembelian::findOrFail($this->transaksiId);

                $oldStatus = $this->originalData['status'];

                // Handle stock changes - kembalikan stok lama
                $this->revertOldStock($transaksi);

                // Update transaksi pembelian dengan status selesai
                $transaksi->update([
                    'id_barang'         => $this->id_barang,
                    'id_supplier'       => $this->id_supplier,
                    'tanggal_transaksi' => $this->tanggal_transaksi,
                    'jumlah_pembelian'  => $this->jumlah_pembelian,
                    'harga'             => $this->harga,
                    'total'             => $this->total,
                    'metode_pembayaran' => $this->metode_pembayaran,
                    'status'            => 'selesai', // Langsung selesai
                ]);

                // Langsung proses transaksi baru sebagai selesai
                $this->processCompletedTransaction($transaksi);

                // Handle journal entries
                $this->handleJournalEntries($transaksi, $oldStatus);
            });

            $this->resetForm();
            $this->dispatch('refreshDatatable');
            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Transaksi pembelian berhasil diupdate dan diselesaikan!'
            ]);
            $this->open = false;

        } catch (\Exception $e) {
            $this->addError('general', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function revertOldStock($transaksi)
    {
        $oldBarangId = $this->originalData['id_barang'];
        $oldJumlah = $this->originalData['jumlah_pembelian'];
        $oldStatus = $this->originalData['status'];

        // Jika transaksi lama statusnya selesai, kembalikan stok
        if ($oldStatus === 'selesai') {
            $oldBarang = Barang::find($oldBarangId);
            if ($oldBarang) {
                $oldBarang->decrement('stok', $oldJumlah);
            }
        }
    }

    private function processCompletedTransaction($transaksi)
    {
        // Tambah stok barang baru
        $newBarang = Barang::find($this->id_barang);
        if ($newBarang) {
            $newBarang->increment('stok', $this->jumlah_pembelian);
            // Update harga beli barang dengan harga terbaru
            $newBarang->update(['harga_beli' => $this->harga]);
        }
    }

    private function handleJournalEntries($transaksi, $oldStatus)
    {
        // Hapus jurnal lama jika ada
        if ($oldStatus === 'selesai') {
            $this->deleteOldJournalEntries($transaksi);
        }

        // Buat jurnal baru karena selalu selesai
        $this->createNewJournalEntries($transaksi);
    }

    private function deleteOldJournalEntries($transaksi)
    {
        JurnalUmum::where('keterangan', 'LIKE', "%Transaksi #{$transaksi->id}%")->delete();
    }

    private function createNewJournalEntries($transaksi)
    {
        // Cari akun yang diperlukan
        $akunPersediaan = Akun::where('tipe_akun', 'Aset')
                             ->where('nama_akun', 'LIKE', '%persediaan%')
                             ->orWhere('nama_akun', 'LIKE', '%inventory%')
                             ->first();

        $akunKas = Akun::where('tipe_akun', 'Aset')
                      ->where('nama_akun', 'LIKE', '%kas%')
                      ->first();

        $akunHutang = Akun::where('tipe_akun', 'Kewajiban')
                         ->where('nama_akun', 'LIKE', '%hutang%')
                         ->first();

        if (!$akunPersediaan) {
            throw new \Exception('Akun Persediaan belum dikonfigurasi.');
        }

        $keterangan = "Update Pembelian {$transaksi->barang->nama_barang} - Transaksi #{$transaksi->id}";

        // Jurnal: Debit Persediaan (Aset bertambah)
        JurnalUmum::create([
            'id_akun'    => $akunPersediaan->id_akun,
            'tanggal'    => $this->tanggal_transaksi,
            'debit'      => $this->total,
            'kredit'     => 0,
            'keterangan' => $keterangan,
        ]);

        // Jurnal: Kredit berdasarkan metode pembayaran
        if ($this->metode_pembayaran === 'credit') {
            // Kredit Hutang (Kewajiban bertambah)
            if (!$akunHutang) {
                throw new \Exception('Akun Hutang belum dikonfigurasi untuk pembayaran kredit.');
            }

            JurnalUmum::create([
                'id_akun'    => $akunHutang->id_akun,
                'tanggal'    => $this->tanggal_transaksi,
                'debit'      => 0,
                'kredit'     => $this->total,
                'keterangan' => $keterangan,
            ]);
        } else {
            // Kredit Kas (Aset berkurang)
            if (!$akunKas) {
                throw new \Exception('Akun Kas belum dikonfigurasi.');
            }

            JurnalUmum::create([
                'id_akun'    => $akunKas->id_akun,
                'tanggal'    => $this->tanggal_transaksi,
                'debit'      => 0,
                'kredit'     => $this->total,
                'keterangan' => $keterangan,
            ]);
        }
    }

    public function getListBarangProperty()
    {
        if (!$this->id_supplier) {
            return collect();
        }

        return Barang::where('id_supplier', $this->id_supplier)
            ->orderBy('nama_barang')
            ->get();
    }

    private function resetForm()
    {
        $this->reset([
            'transaksiId', 'id_barang', 'id_supplier', 'tanggal_transaksi',
            'jumlah_pembelian', 'harga', 'total', 'originalData'
        ]);
        $this->metode_pembayaran = 'cash';
        // Hapus reset status
    }

    public function closeModal()
    {
        $this->resetForm();
        $this->resetErrorBag();
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.transaksi-pembelian.update', [
            'listSupplier' => Supplier::orderBy('nama_supplier')->get(),
            'listBarang' => $this->listBarang,
            'selectedSupplier' => $this->id_supplier ? Supplier::where('id_supplier', $this->id_supplier)->first() : null,
            'selectedBarang' => $this->id_barang ? Barang::find($this->id_barang) : null,
        ]);
    }
}
