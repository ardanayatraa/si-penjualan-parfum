<?php

namespace App\Livewire\TransaksiPenjualan;

use Livewire\Component;
use App\Models\TransaksiPenjualan;
use App\Models\Barang;
use App\Models\PajakTransaksi;
use App\Models\JurnalUmum;
use App\Models\Akun;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class Create extends Component
{
    public $open = false;

    // Form fields
    public $id_barang;
    public $jumlah_terjual = 1;
    public $tanggal_transaksi;
    public $id_pajak;
    public $metode_pembayaran = 'cash';

    // Calculated fields
    public $harga_jual = 0;
    public $harga_pokok = 0;
    public $subtotal = 0;
    public $pajak_amount = 0;
    public $total_harga = 0;
    public $laba_bruto = 0;

    // Available options
    public $metodePembayaranOptions = [
        'cash' => 'Tunai',
        'qris' => 'Qris',
        'piutang' => 'Piutang',
    ];

    protected $rules = [
        'id_barang'          => 'required|exists:barang,id',
        'jumlah_terjual'     => 'required|integer|min:1',
        'tanggal_transaksi'  => 'required|date',
        'metode_pembayaran'  => 'required|in:cash,qris,piutang',
    ];

    protected $messages = [
        'id_barang.required'         => 'Barang harus dipilih.',
        'id_barang.exists'           => 'Barang tidak valid.',
        'jumlah_terjual.required'    => 'Jumlah penjualan harus diisi.',
        'jumlah_terjual.min'         => 'Jumlah penjualan minimal 1.',
        'tanggal_transaksi.required' => 'Tanggal transaksi harus diisi.',
        'metode_pembayaran.required' => 'Metode pembayaran harus dipilih.',
    ];

    public function mount()
    {
        $this->tanggal_transaksi = Carbon::now()->toDateString();
        $this->id_pajak = PajakTransaksi::first()?->id ?? null;
    }

    public function updatedIdBarang($value)
    {
        $barang = Barang::find($value);
        if ($barang) {
            $this->harga_jual = $barang->harga_jual;
            $this->harga_pokok = $barang->harga_beli;

            // Validasi stok
            if ($this->jumlah_terjual > $barang->stok) {
                $this->jumlah_terjual = $barang->stok;
                if ($barang->stok == 0) {
                    $this->addError('id_barang', 'Barang ini stoknya habis.');
                } else {
                    $this->addError('jumlah_terjual', "Stok hanya tersedia {$barang->stok} unit.");
                }
            }
        } else {
            $this->reset(['harga_jual', 'harga_pokok']);
        }
        $this->recalculate();
    }

    public function updatedJumlahTerjual($value)
    {
        $barang = Barang::find($this->id_barang);
        if ($barang) {
            if ($value > $barang->stok) {
                $this->addError('jumlah_terjual', "Jumlah penjualan tidak boleh melebihi stok ({$barang->stok}).");
                $this->jumlah_terjual = $barang->stok;
            } else {
                $this->resetErrorBag('jumlah_terjual');
            }
        }
        $this->recalculate();
    }

    public function updatedIdPajak($value)
    {
        $this->recalculate();
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

    public function store()
    {
        $this->validate();

        $barang = Barang::find($this->id_barang);
        if (!$barang) {
            $this->addError('id_barang', 'Barang tidak ditemukan.');
            return;
        }

        if ($this->jumlah_terjual > $barang->stok) {
            $this->addError('jumlah_terjual', "Jumlah melebihi stok tersedia ({$barang->stok}).");
            return;
        }

        try {
            DB::transaction(function () use ($barang) {
                // Create transaksi penjualan langsung dengan status selesai
                $transaksi = TransaksiPenjualan::create([
                    'id_kasir'           => Auth::id(),
                    'id_barang'          => $this->id_barang,
                    'id_pajak'           => $this->id_pajak,
                    'tanggal_transaksi'  => $this->tanggal_transaksi,
                    'jumlah_terjual'     => $this->jumlah_terjual,
                    'subtotal'           => $this->subtotal,
                    'harga_pokok'        => $this->harga_pokok,
                    'laba_bruto'         => $this->laba_bruto,
                    'total_harga'        => $this->total_harga,
                    'metode_pembayaran'  => $this->metode_pembayaran,
                    'status'             => 'selesai',
                ]);

                // Jika piutang, buat piutang
                if ($this->metode_pembayaran === 'piutang') {
                    \App\Models\Piutang::create([
                        'id_penjualan' => $transaksi->id,
                        'jumlah' => $this->total_harga,
                        'jumlah_dibayarkan' => 0,
                        'status' => 'belum lunas',
                        'nama_pelanggan' => Auth::user()->name ?? '-',
                        'no_telp' => Auth::user()->no_telp ?? '-',
                    ]);
                }

                // Langsung proses transaksi selesai
                $this->processCompletedTransaction($transaksi, $barang);
            });

            $this->resetForm();
            $this->dispatch('refreshDatatable');
            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Transaksi penjualan berhasil diselesaikan!'
            ]);
            $this->open = false;

        } catch (\Exception $e) {
            $this->addError('general', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function processCompletedTransaction($transaksi, $barang)
    {
        // Update stok barang
        $barang->decrement('stok', $this->jumlah_terjual);

        // Buat jurnal umum LENGKAP
        $this->createJournalEntries($transaksi, $barang);
    }

    private function createJournalEntries($transaksi, $barang)
    {
        // Cari akun yang diperlukan
        $akunKas = $this->metode_pembayaran === 'piutang' 
            ? Akun::where('kode_akun', '1.2.01')->first() // Piutang Dagang
            : Akun::where('kode_akun', '1.1.01')->first(); // Kas

        $akunPendapatan = Akun::where('kode_akun', '4.1.01')->first(); // Penjualan Barang
        $akunHPP = Akun::where('kode_akun', '5.1.03')->first(); // Beban HPP
        $akunPersediaan = Akun::where('kode_akun', '1.1.05')->first(); // Persediaan Barang
        $akunPPN = $this->pajak_amount > 0 ? Akun::where('kode_akun', '2.1.02')->first() : null; // PPN Keluaran

        // Validasi akun
        if (!$akunKas || !$akunPendapatan || !$akunHPP || !$akunPersediaan) {
            throw new \Exception('Akun belum dikonfigurasi lengkap. Silakan hubungi administrator.');
        }

        $keterangan = "Penjualan {$barang->nama_barang} - Qty: {$this->jumlah_terjual} - Transaksi #{$transaksi->id}";

        // === JURNAL PENDAPATAN ===
        // Debit: Kas/Piutang (Aset bertambah)
        JurnalUmum::create([
            'id_akun'    => $akunKas->id_akun,
            'tanggal'    => $this->tanggal_transaksi,
            'debit'      => $this->total_harga, // Total termasuk pajak
            'kredit'     => 0,
            'keterangan' => $keterangan,
        ]);

        // Kredit: Penjualan Barang (Pendapatan bertambah)
        JurnalUmum::create([
            'id_akun'    => $akunPendapatan->id_akun,
            'tanggal'    => $this->tanggal_transaksi,
            'debit'      => 0,
            'kredit'     => $this->subtotal, // Subtotal tanpa pajak
            'keterangan' => $keterangan,
        ]);

        // === JURNAL HPP ===
        $totalHPP = $this->harga_pokok * $this->jumlah_terjual;

        // Debit: Beban HPP (Beban bertambah)
        JurnalUmum::create([
            'id_akun'    => $akunHPP->id_akun,
            'tanggal'    => $this->tanggal_transaksi,
            'debit'      => $totalHPP,
            'kredit'     => 0,
            'keterangan' => "HPP {$barang->nama_barang} - Qty: {$this->jumlah_terjual} - Transaksi #{$transaksi->id}",
        ]);

        // Kredit: Persediaan (Aset berkurang)
        JurnalUmum::create([
            'id_akun'    => $akunPersediaan->id_akun,
            'tanggal'    => $this->tanggal_transaksi,
            'debit'      => 0,
            'kredit'     => $totalHPP,
            'keterangan' => "HPP {$barang->nama_barang} - Qty: {$this->jumlah_terjual} - Transaksi #{$transaksi->id}",
        ]);

        // === JURNAL PAJAK (jika ada) ===
        if ($this->pajak_amount > 0 && $akunPPN) {
            // Kredit: PPN Keluaran (Kewajiban bertambah)
            JurnalUmum::create([
                'id_akun'    => $akunPPN->id_akun,
                'tanggal'    => $this->tanggal_transaksi,
                'debit'      => 0,
                'kredit'     => $this->pajak_amount,
                'keterangan' => "PPN Penjualan - Transaksi #{$transaksi->id}",
            ]);
        }

        // Log untuk debugging
        \Log::info("Jurnal Penjualan Created - Transaksi #{$transaksi->id}: Pendapatan: {$this->subtotal}, HPP: {$totalHPP}, Pajak: {$this->pajak_amount}");
    }

    private function resetForm()
    {
        $this->reset([
            'id_barang',
            'jumlah_terjual',
            'harga_jual',
            'harga_pokok',
            'subtotal',
            'pajak_amount',
            'total_harga',
            'laba_bruto'
        ]);

        $this->jumlah_terjual = 1;
        $this->metode_pembayaran = 'cash';
        $this->tanggal_transaksi = Carbon::now()->toDateString();
    }

    public function closeModal()
    {
        $this->resetForm();
        $this->resetErrorBag();
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.transaksi-penjualan.create', [
            'listBarang' => Barang::where('stok', '>', 0)
                                 ->orderBy('nama_barang')
                                 ->get(),
            'listPajak' => PajakTransaksi::orderBy('presentase')
                                        ->get(),
            'selectedBarang' => $this->id_barang ? Barang::find($this->id_barang) : null,
        ]);
    }
}