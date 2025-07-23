<?php

namespace App\Livewire\ReturnBarang;

use Livewire\Component;
use App\Models\ReturnBarang;
use App\Models\Barang;
use App\Models\JurnalUmum;
use App\Models\Akun;
use App\Models\TransaksiPembelian;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CreateReturn extends Component
{
    public $open = false;
    public $transaksiId = null; // untuk create dari transaksi pembelian
    public $id_barang;
    public $jumlah;
    public $alasan;
    public $tanggal_return;
    public $availableStok = 0;

    protected $listeners = ['createReturn' => 'loadFromTransaksi', 'openCreate' => 'openModal'];

    protected $rules = [
        'id_barang'       => 'required|exists:barang,id',
        'jumlah'          => 'required|integer|min:1',
        'alasan'          => 'required|string|max:500',
        'tanggal_return'  => 'required|date',
    ];

    protected $messages = [
        'id_barang.required'      => 'Barang harus dipilih.',
        'id_barang.exists'        => 'Barang tidak valid.',
        'jumlah.required'         => 'Jumlah harus diisi.',
        'jumlah.min'              => 'Jumlah minimal 1.',
        'alasan.required'         => 'Alasan return harus diisi.',
        'alasan.max'              => 'Alasan maksimal 500 karakter.',
        'tanggal_return.required' => 'Tanggal return harus diisi.',
        'tanggal_return.date'     => 'Format tanggal tidak valid.',
    ];

    public function mount()
    {
        $this->tanggal_return = Carbon::now()->format('Y-m-d');
    }

    public function openModal()
    {
        $this->resetForm();
        $this->tanggal_return = Carbon::now()->format('Y-m-d');
        $this->open = true;
    }

    public function loadFromTransaksi($transaksiId)
    {
        $transaksi = TransaksiPembelian::with('barang')->findOrFail($transaksiId);

        $this->transaksiId = $transaksiId;
        $this->id_barang = $transaksi->barang->id;
        $this->jumlah = 1; // default 1, user bisa ubah
        $this->tanggal_return = Carbon::now()->format('Y-m-d');

        // Hitung stok tersedia
        $this->availableStok = $transaksi->barang->stok;

        $this->open = true;
    }

    public function updatedIdBarang($value)
    {
        if ($value) {
            $barang = Barang::find($value);
            $this->availableStok = $barang ? $barang->stok : 0;

            // Reset jumlah jika melebihi stok tersedia
            if ($this->jumlah > $this->availableStok) {
                $this->jumlah = min($this->jumlah, $this->availableStok);
            }
        }
    }

    public function updatedJumlah($value)
    {
        if ($value > $this->availableStok) {
            $this->addError(
                'jumlah',
                "Jumlah return tidak boleh melebihi stok tersedia ({$this->availableStok})."
            );
            $this->jumlah = $this->availableStok;
        } else {
            $this->resetErrorBag('jumlah');
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

        // Cek stok sekali lagi sebelum menyimpan
        if ($this->jumlah > $barang->stok) {
            $this->addError(
                'jumlah',
                "Jumlah return tidak boleh melebihi stok tersedia ({$barang->stok})."
            );
            return;
        }

        try {
            DB::transaction(function () use ($barang) {
                // 1. Buat record return barang
                $return = ReturnBarang::create([
                    'id_barang'      => $this->id_barang,
                    'jumlah'         => $this->jumlah,
                    'id_supplier'    => $barang->id_supplier,
                    'alasan'         => $this->alasan,
                    'tanggal_return' => $this->tanggal_return,
                ]);

                // 2. Kurangi stok barang
                $barang->decrement('stok', $this->jumlah);

                // 3. Buat jurnal return
                $this->buatJurnalReturn($return, $barang);
            });

            $this->resetForm();
            $this->dispatch('refreshDatatable');
            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Return barang berhasil dibuat!'
            ]);
            $this->open = false;

        } catch (\Exception $e) {
            $this->addError('general', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    protected function buatJurnalReturn(ReturnBarang $return, Barang $barang)
    {
        // Cari akun yang diperlukan
        $akunHutang = Akun::where('tipe_akun', 'Kewajiban')
                         ->where('nama_akun', 'LIKE', '%hutang%')
                         ->first();

        $akunPersediaan = Akun::where('tipe_akun', 'Aset')
                             ->where('nama_akun', 'LIKE', '%persediaan%')
                             ->orWhere('nama_akun', 'LIKE', '%inventory%')
                             ->first();

        // Fallback jika tidak ditemukan berdasarkan nama
        if (!$akunHutang) {
            $akunHutang = Akun::where('kode_akun', 'LIKE', '2.1%')->first();
        }

        if (!$akunPersediaan) {
            $akunPersediaan = Akun::where('kode_akun', 'LIKE', '1.1%')->first();
        }

        if (!$akunHutang || !$akunPersediaan) {
            throw new \Exception('Akun Hutang atau Persediaan belum dikonfigurasi.');
        }

        $nilai = $barang->harga_beli * $return->jumlah;
        $keterangan = "Return barang {$barang->nama_barang} - Return ID {$return->id}";

        // Jurnal: Debit Hutang Dagang (Kewajiban berkurang)
        JurnalUmum::create([
            'id_akun'    => $akunHutang->id_akun,
            'tanggal'    => $return->tanggal_return,
            'debit'      => $nilai,
            'kredit'     => 0,
            'keterangan' => $keterangan,
        ]);

        // Jurnal: Kredit Persediaan (Aset berkurang)
        JurnalUmum::create([
            'id_akun'    => $akunPersediaan->id_akun,
            'tanggal'    => $return->tanggal_return,
            'debit'      => 0,
            'kredit'     => $nilai,
            'keterangan' => $keterangan,
        ]);
    }

    public function getSelectedBarangProperty()
    {
        return $this->id_barang ? Barang::find($this->id_barang) : null;
    }

    private function resetForm()
    {
        $this->reset([
            'transaksiId',
            'id_barang',
            'jumlah',
            'alasan',
            'availableStok',
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
        return view('livewire.return-barang.create-return', [
            'listBarang' => Barang::with('supplier')
                                 ->where('stok', '>', 0) // hanya barang yang ada stoknya
                                 ->orderBy('nama_barang')
                                 ->get(),
            'selectedBarang' => $this->selectedBarang,
        ]);
    }
}
