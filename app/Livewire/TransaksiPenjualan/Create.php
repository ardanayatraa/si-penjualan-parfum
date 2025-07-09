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
    public $jumlah_terjual = 1; // Sesuai dengan field di model
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

    // Available options
    public $metodePembayaranOptions = [
        'cash' => 'Tunai',
        'qris' => 'Qris',
    ];

    protected $rules = [
        'id_barang'          => 'required|exists:barang,id',
        'jumlah_terjual'     => 'required|integer|min:1',
        'tanggal_transaksi'  => 'required|date',
        'metode_pembayaran'  => 'required|in:cash,transfer,debit_card,credit_card,e_wallet',
        'status'             => 'required|in:pending,selesai,dibatalkan',
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
                // Create transaksi penjualan
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
                    'status'             => $this->status,
                ]);

                // Jika status selesai, update stok dan buat jurnal
                if ($this->status === 'selesai') {
                    $this->processCompletedTransaction($transaksi, $barang);
                }
            });

            $this->resetForm();
            $this->dispatch('refreshDatatable');
            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Transaksi penjualan berhasil disimpan!'
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

        // Buat jurnal umum
        $this->createJournalEntries($transaksi);
    }

    private function createJournalEntries($transaksi)
    {
        // Cari akun yang diperlukan
        $akunKas = Akun::where('tipe_akun', 'Aset')
                      ->where('nama_akun', 'LIKE', '%kas%')
                      ->first();

        $akunPenjualan = Akun::where('tipe_akun', 'Pendapatan')
                            ->where('nama_akun', 'LIKE', '%penjualan%')
                            ->first();

        if (!$akunKas || !$akunPenjualan) {
            throw new \Exception('Akun Kas atau Penjualan belum dikonfigurasi. Silakan hubungi administrator.');
        }

        $keterangan = "Penjualan {$transaksi->barang->nama_barang} - Transaksi #{$transaksi->id}";

        // Jurnal: Debit Kas (Aset bertambah)
        JurnalUmum::create([
            'id_akun'    => $akunKas->id_akun,
            'tanggal'    => $this->tanggal_transaksi,
            'debit'      => $this->total_harga,
            'kredit'     => 0,
            'keterangan' => $keterangan,
        ]);

        // Jurnal: Kredit Penjualan (Pendapatan bertambah)
        JurnalUmum::create([
            'id_akun'    => $akunPenjualan->id_akun,
            'tanggal'    => $this->tanggal_transaksi,
            'debit'      => 0,
            'kredit'     => $this->total_harga,
            'keterangan' => $keterangan,
        ]);
    }

    public function setStatusSelesai()
    {
        $this->status = 'selesai';
    }

    public function setStatusPending()
    {
        $this->status = 'pending';
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
        $this->status = 'pending';
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
