<?php

namespace App\Livewire\TransaksiPembelian;

use Livewire\Component;
use App\Models\TransaksiPembelian;
use App\Models\Barang;
use App\Models\Supplier;
use App\Models\JurnalUmum;
use App\Models\Akun;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Create extends Component
{
    public $open = false;

    // Form fields
    public $id_supplier;
    public $tanggal_transaksi;
    public $metode_pembayaran = 'cash';
    public $status = 'pending';

    // Shopping cart
    public $quantities = [];
    public $harga_satuan = [];

    // UI helpers
    public $search = '';

    // Payment method options
    public $metodePembayaranOptions = [
        'cash' => 'Tunai',
        'qris' => 'Qris',

    ];

    protected function rules()
    {
        return [
            'id_supplier'        => 'required|exists:supplier,id_supplier',
            'tanggal_transaksi'  => 'required|date',
            'metode_pembayaran'  => 'required|in:cash,qris',
            'status'             => 'required|in:pending,selesai',
            'quantities'         => 'required|array|min:1',
            'quantities.*'       => 'integer|min:1',
            'harga_satuan'       => 'required|array',
            'harga_satuan.*'     => 'numeric|min:0',
        ];
    }

    protected $messages = [
        'id_supplier.required'        => 'Supplier harus dipilih.',
        'tanggal_transaksi.required'  => 'Tanggal transaksi harus diisi.',
        'quantities.required'         => 'Minimal satu barang harus dipilih.',
        'quantities.*.min'            => 'Jumlah pembelian minimal 1.',
        'harga_satuan.*.min'          => 'Harga tidak boleh negatif.',
    ];

    public function mount()
    {
        $this->tanggal_transaksi = Carbon::now()->toDateString();
        $this->quantities = [];
        $this->harga_satuan = [];
    }

    public function updatedIdSupplier($supplierId)
    {
        $this->resetCart();

        if ($supplierId) {
            // Debug log
            logger('Supplier selected: ' . $supplierId);

            // Initialize quantities dan harga untuk semua barang supplier
            $barangs = Barang::where('id_supplier', $supplierId)->get();

            // Debug log
            logger('Found ' . $barangs->count() . ' barang for supplier ' . $supplierId);

            foreach ($barangs as $barang) {
                $this->quantities[$barang->id] = 0;
                $this->harga_satuan[$barang->id] = $barang->harga_beli;
                logger('Initialized barang ' . $barang->id . ' (' . $barang->nama_barang . ')');
            }

            // Force re-render
            $this->dispatch('$refresh');
        }
    }

    public function increase($barangId)
    {
        // Ensure key exists
        if (!isset($this->quantities[$barangId])) {
            $this->quantities[$barangId] = 0;
        }
        $this->quantities[$barangId]++;

        // Debug log
        logger('Increased quantity for barang ' . $barangId . ' to ' . $this->quantities[$barangId]);
    }

    public function decrease($barangId)
    {
        // Ensure key exists
        if (!isset($this->quantities[$barangId])) {
            $this->quantities[$barangId] = 0;
        }

        if ($this->quantities[$barangId] > 0) {
            $this->quantities[$barangId]--;
        }

        // Debug log
        logger('Decreased quantity for barang ' . $barangId . ' to ' . $this->quantities[$barangId]);
    }

    public function updateHarga($barangId, $harga)
    {
        $this->harga_satuan[$barangId] = max(0, (float)$harga);
    }

    public function addToCart($barangId, $quantity = 1)
    {
        $this->quantities[$barangId] = ($this->quantities[$barangId] ?? 0) + $quantity;
    }

    public function removeFromCart($barangId)
    {
        unset($this->quantities[$barangId]);
        unset($this->harga_satuan[$barangId]);
    }

    public function resetCart()
    {
        $this->quantities = [];
        $this->harga_satuan = [];
        $this->search = '';
    }

    public function store()
    {
        // Filter hanya barang yang ada quantity > 0
        $selectedItems = collect($this->quantities)
            ->filter(fn($qty) => $qty > 0);

        if ($selectedItems->isEmpty()) {
            $this->addError('quantities', 'Minimal satu barang dengan jumlah > 0 harus dipilih.');
            return;
        }

        // Prepare validation data
        $this->quantities = $selectedItems->toArray();
        $this->harga_satuan = collect($this->harga_satuan)
            ->only($selectedItems->keys())
            ->toArray();

        $this->validate();

        try {
            DB::transaction(function() use ($selectedItems) {
                foreach ($selectedItems as $barangId => $quantity) {
                    $barang = Barang::find($barangId);
                    if (!$barang) continue;

                    $harga = $this->harga_satuan[$barangId];
                    $total = $harga * $quantity;

                    // Create transaksi pembelian
                    $transaksi = TransaksiPembelian::create([
                        'id_barang'          => $barangId,
                        'id_supplier'        => $this->id_supplier,
                        'tanggal_transaksi'  => $this->tanggal_transaksi,
                        'jumlah_pembelian'   => $quantity,
                        'harga'              => $harga,
                        'total'              => $total,
                        'metode_pembayaran'  => $this->metode_pembayaran,
                        'status'             => $this->status,
                    ]);

                    // Jika status selesai, update stok dan buat jurnal
                    if ($this->status === 'selesai') {
                        $this->processCompletedTransaction($transaksi, $barang, $quantity, $harga, $total);
                    }
                }
            });

            $this->resetForm();
            $this->dispatch('refreshDatatable');
            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Transaksi pembelian berhasil disimpan!'
            ]);
            $this->open = false;

        } catch (\Exception $e) {
            $this->addError('general', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function processCompletedTransaction($transaksi, $barang, $quantity, $harga, $total)
    {
        // Update stok barang
        $barang->increment('stok', $quantity);

        // Update harga beli barang dengan harga terbaru
        $barang->update(['harga_beli' => $harga]);

        // Buat jurnal umum
        $this->createJournalEntries($transaksi, $barang, $total);
    }

    private function createJournalEntries($transaksi, $barang, $total)
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

        $keterangan = "Pembelian {$barang->nama_barang} - Transaksi #{$transaksi->id}";

        // Jurnal: Debit Persediaan (Aset bertambah)
        JurnalUmum::create([
            'id_akun'    => $akunPersediaan->id_akun,
            'tanggal'    => $this->tanggal_transaksi,
            'debit'      => $total,
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
                'kredit'     => $total,
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
                'kredit'     => $total,
                'keterangan' => $keterangan,
            ]);
        }
    }

    public function getTotalBelanjaProperty()
    {
        $total = 0;
        foreach ($this->quantities as $barangId => $quantity) {
            if ($quantity > 0) {
                $harga = $this->harga_satuan[$barangId] ?? 0;
                $total += $harga * $quantity;
            }
        }
        return $total;
    }

    public function getSelectedItemsProperty()
    {
        return collect($this->quantities)
            ->filter(fn($qty) => $qty > 0)
            ->map(function($qty, $barangId) {
                $barang = Barang::find($barangId);
                return [
                    'barang' => $barang,
                    'quantity' => $qty,
                    'harga' => $this->harga_satuan[$barangId] ?? 0,
                    'subtotal' => ($this->harga_satuan[$barangId] ?? 0) * $qty,
                ];
            });
    }

    public function getListBarangProperty()
    {
        if (!$this->id_supplier) {
            return collect();
        }

        // Debug log
        logger('Getting barang for supplier: ' . $this->id_supplier);

        return Barang::where('id_supplier', $this->id_supplier)
            ->when($this->search, fn($q) =>
                $q->where('nama_barang', 'like', '%' . $this->search . '%')
            )
            ->orderBy('nama_barang')
            ->get();
    }

    private function resetForm()
    {
        $this->reset([
            'id_supplier', 'quantities', 'harga_satuan', 'search'
        ]);
        $this->tanggal_transaksi = Carbon::now()->toDateString();
        $this->metode_pembayaran = 'cash';
        $this->status = 'pending';
    }

    public function closeModal()
    {
        $this->resetForm();
        $this->resetErrorBag();
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.transaksi-pembelian.create', [
            'suppliers' => Supplier::orderBy('nama_supplier')->get(),
            'listBarang' => $this->listBarang,
            'totalBelanja' => $this->totalBelanja,
            'selectedItems' => $this->selectedItems,
            'selectedSupplier' => $this->id_supplier ? Supplier::where('id_supplier', $this->id_supplier)->first() : null,
        ]);
    }
}
