<?php

namespace App\Livewire\TransaksiPenjualan;

use Livewire\Component;
use App\Models\TransaksiPenjualan;
use App\Models\Barang;
use App\Models\JurnalUmum;
use App\Models\Akun;
use App\Models\PajakTransaksi;
use Illuminate\Support\Facades\DB;

class Update extends Component
{
    public $open = false;
    public $id_transaksi;

    // Form fields - sesuai dengan model baru
    public $id_barang;
    public $jumlah_terjual = 1;
    public $tanggal_transaksi;
    public $id_pajak;
    public $metode_pembayaran = 'cash';
    public $status = 'pending';

    // Calculated fields
    public $harga_jual = 0;
    public $harga_pokok = 0;
    public $subtotal = 0;
    public $pajak_amount = 0;
    public $total_harga = 0;
    public $laba_bruto = 0;

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
            'id_barang'          => 'required|exists:barang,id',
            'jumlah_terjual'     => 'required|integer|min:1',
            'tanggal_transaksi'  => 'required|date',
            'metode_pembayaran'  => 'required',
            'status'             => 'required|in:pending,selesai,dibatalkan',
        ];
    }

    protected $messages = [
        'id_barang.required'         => 'Barang harus dipilih.',
        'jumlah_terjual.required'    => 'Jumlah penjualan harus diisi.',
        'jumlah_terjual.min'         => 'Jumlah penjualan minimal 1.',
        'tanggal_transaksi.required' => 'Tanggal transaksi harus diisi.',
        'metode_pembayaran.required' => 'Metode pembayaran harus dipilih.',
        'status.required'            => 'Status harus dipilih.',
    ];

    public function loadData($id)
    {
        $transaksi = TransaksiPenjualan::find($id);
        if (!$transaksi) {
            $this->dispatch('showError', message: 'Transaksi tidak ditemukan');
            return;
        }

        // Store original data untuk tracking perubahan
        $this->originalData = [
            'id_barang' => $transaksi->id_barang,
            'jumlah_terjual' => $transaksi->jumlah_terjual,
            'status' => $transaksi->status,
        ];

        // Load data ke form
        $this->id_transaksi = $transaksi->id;
        $this->id_barang = $transaksi->id_barang;
        $this->jumlah_terjual = $transaksi->jumlah_terjual;
        $this->tanggal_transaksi = $transaksi->tanggal_transaksi->format('Y-m-d');
        $this->id_pajak = $transaksi->id_pajak;
        $this->metode_pembayaran = $transaksi->metode_pembayaran;
        $this->status = $transaksi->status;

        // Load calculated fields
        $this->subtotal = $transaksi->subtotal;
        $this->harga_pokok = $transaksi->harga_pokok;
        $this->laba_bruto = $transaksi->laba_bruto;
        $this->total_harga = $transaksi->total_harga;

        // Load barang data
        if ($barang = Barang::find($this->id_barang)) {
            $this->harga_jual = $barang->harga_jual;
        }

        // Calculate pajak amount
        if ($this->id_pajak && $pajak = PajakTransaksi::find($this->id_pajak)) {
            $this->pajak_amount = ($this->subtotal * $pajak->presentase) / 100;
        }

        $this->open = true;
    }

    public function updatedIdBarang($value)
    {
        $barang = Barang::find($value);
        if ($barang) {
            $this->harga_jual = $barang->harga_jual;
            $this->harga_pokok = $barang->harga_beli;

            // Validasi stok untuk barang baru
            if ($value != $this->originalData['id_barang']) {
                if ($this->jumlah_terjual > $barang->stok) {
                    $this->jumlah_terjual = $barang->stok;
                    if ($barang->stok == 0) {
                        $this->addError('id_barang', 'Barang ini stoknya habis.');
                    }
                }
            }
        } else {
            $this->reset(['harga_jual', 'harga_pokok']);
        }
        $this->recalculate();
    }

    public function updatedJumlahTerjual($value)
    {
        $this->validateStok();
        $this->recalculate();
    }

    public function updatedIdPajak($value)
    {
        $this->recalculate();
    }

    private function validateStok()
    {
        $barang = Barang::find($this->id_barang);
        if (!$barang) return;

        $stokTersedia = $barang->stok;

        // Jika barang sama, tambahkan stok yang sedang digunakan transaksi ini
        if ($this->id_barang == $this->originalData['id_barang']) {
            $stokTersedia += $this->originalData['jumlah_terjual'];
        }

        if ($this->jumlah_terjual > $stokTersedia) {
            $this->addError('jumlah_terjual', "Stok tidak mencukupi. Tersedia: {$stokTersedia} unit.");
            $this->jumlah_terjual = $stokTersedia;
        } else {
            $this->resetErrorBag('jumlah_terjual');
        }
    }

    private function recalculate()
    {
        if (!$this->harga_jual || !$this->jumlah_terjual) {
            $this->reset(['subtotal', 'pajak_amount', 'total_harga', 'laba_bruto']);
            return;
        }

        // Hitung subtotal
        $this->subtotal = $this->harga_jual * $this->jumlah_terjual;

        // Hitung pajak
        if ($pajak = PajakTransaksi::find($this->id_pajak)) {
            $this->pajak_amount = round(($this->subtotal * $pajak->presentase) / 100, 2);
        } else {
            $this->pajak_amount = 0;
        }

        // Hitung total harga
        $this->total_harga = $this->subtotal + $this->pajak_amount;

        // Hitung laba bruto
        if ($this->harga_pokok) {
            $total_hpp = $this->harga_pokok * $this->jumlah_terjual;
            $this->laba_bruto = $this->subtotal - $total_hpp;
        }
    }

    public function update()
    {
        $this->validate();

        try {
            DB::transaction(function () {
                $transaksi = TransaksiPenjualan::find($this->id_transaksi);
                if (!$transaksi) {
                    throw new \Exception('Transaksi tidak ditemukan');
                }

                $oldStatus = $transaksi->status;
                $newStatus = $this->status;

                // Handle stock changes
                $this->handleStockChanges($transaksi);

                // Update transaksi
                $transaksi->update([
                    'id_barang'          => $this->id_barang,
                    'id_pajak'           => $this->id_pajak,
                    'tanggal_transaksi'  => $this->tanggal_transaksi,
                    'jumlah_terjual'     => $this->jumlah_terjual,
                    'subtotal'           => $this->subtotal,
                    'harga_pokok'        => $this->harga_pokok,
                    'laba_bruto'         => $this->laba_bruto,
                    'total_harga'        => $this->total_harga,
                    'metode_pembayaran'  => $this->metode_pembayaran,
                    'status'             => $this->status,
                ]);

                // Handle journal entries based on status change
                $this->handleJournalEntries($transaksi, $oldStatus, $newStatus);
            });

            $this->resetForm();
            $this->dispatch('refreshDatatable');
            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Transaksi berhasil diupdate!'
            ]);
            $this->open = false;

        } catch (\Exception $e) {
            $this->addError('general', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function handleStockChanges($transaksi)
    {
        $oldBarangId = $this->originalData['id_barang'];
        $oldJumlah = $this->originalData['jumlah_terjual'];
        $oldStatus = $this->originalData['status'];

        // Jika transaksi lama statusnya selesai, kembalikan stok
        if ($oldStatus === 'selesai') {
            $oldBarang = Barang::find($oldBarangId);
            if ($oldBarang) {
                $oldBarang->increment('stok', $oldJumlah);
            }
        }

        // Jika transaksi baru statusnya selesai, kurangi stok
        if ($this->status === 'selesai') {
            $newBarang = Barang::find($this->id_barang);
            if ($newBarang) {
                if ($newBarang->stok < $this->jumlah_terjual) {
                    throw new \Exception("Stok {$newBarang->nama_barang} tidak mencukupi.");
                }
                $newBarang->decrement('stok', $this->jumlah_terjual);
            }
        }
    }

    private function handleJournalEntries($transaksi, $oldStatus, $newStatus)
    {
        // Hapus jurnal lama jika ada
        if ($oldStatus === 'selesai') {
            $this->deleteOldJournalEntries($transaksi);
        }

        // Buat jurnal baru jika status selesai
        if ($newStatus === 'selesai') {
            $this->createNewJournalEntries($transaksi);
        }
    }

    private function deleteOldJournalEntries($transaksi)
    {
        JurnalUmum::where('keterangan', 'LIKE', "%Transaksi #{$transaksi->id}%")->delete();
    }

    private function createNewJournalEntries($transaksi)
    {
        // Cari akun yang diperlukan
        $akunKas = Akun::where('tipe_akun', 'Aset')
                      ->where('nama_akun', 'LIKE', '%kas%')
                      ->first();

        $akunPenjualan = Akun::where('tipe_akun', 'Pendapatan')
                            ->where('nama_akun', 'LIKE', '%penjualan%')
                            ->first();

        if (!$akunKas || !$akunPenjualan) {
            throw new \Exception('Akun Kas atau Penjualan belum dikonfigurasi.');
        }

        $keterangan = "Update Penjualan {$transaksi->barang->nama_barang} - Transaksi #{$transaksi->id}";

        // Jurnal: Debit Kas
        JurnalUmum::create([
            'id_akun'    => $akunKas->id_akun,
            'tanggal'    => $this->tanggal_transaksi,
            'debit'      => $this->total_harga,
            'kredit'     => 0,
            'keterangan' => $keterangan,
        ]);

        // Jurnal: Kredit Penjualan
        JurnalUmum::create([
            'id_akun'    => $akunPenjualan->id_akun,
            'tanggal'    => $this->tanggal_transaksi,
            'debit'      => 0,
            'kredit'     => $this->total_harga,
            'keterangan' => $keterangan,
        ]);
    }

    private function resetForm()
    {
        $this->reset([
            'id_transaksi', 'id_barang', 'jumlah_terjual',
            'tanggal_transaksi', 'id_pajak', 'metode_pembayaran', 'status',
            'harga_jual', 'harga_pokok', 'subtotal', 'pajak_amount',
            'total_harga', 'laba_bruto', 'originalData'
        ]);
    }

    public function closeModal()
    {
        $this->resetForm();
        $this->resetErrorBag();
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.transaksi-penjualan.update', [
            'listBarang' => Barang::where('stok', '>', 0)
                                 ->orWhere('id', $this->id_barang)
                                 ->orderBy('nama_barang')
                                 ->get(),
            'listPajak' => PajakTransaksi::orderBy('presentase')->get(),
            'selectedBarang' => $this->id_barang ? Barang::find($this->id_barang) : null,
        ]);
    }
}
