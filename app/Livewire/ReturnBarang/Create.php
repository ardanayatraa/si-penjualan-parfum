<?php

namespace App\Livewire\ReturnBarang;

use Livewire\Component;
use App\Models\ReturnBarang;
use App\Models\Barang;
use App\Models\Supplier;
use App\Models\JurnalUmum;
use App\Models\Akun;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Create extends Component
{
    public $open = false;
    public $id_barang;
    public $jumlah = 1;
    public $alasan;
    public $tanggal_return;

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

    protected $listeners = ['returnFromPenjualan' => 'prefillFromPenjualan'];

    public function mount()
    {
        $this->tanggal_return = Carbon::now()->toDateString();
    }

    public function updatedIdBarang($value)
    {
        if ($value) {
            $barang = Barang::find($value);
            if ($barang && $this->jumlah > $barang->stok) {
                $this->jumlah = min($this->jumlah, $barang->stok);
            }
        }
    }

    public function updatedJumlah($value)
    {
        if ($this->id_barang) {
            $barang = Barang::find($this->id_barang);
            if ($barang && $value > $barang->stok) {
                $this->addError(
                    'jumlah',
                    "Jumlah return tidak boleh melebihi stok saat ini ({$barang->stok})."
                );
                $this->jumlah = $barang->stok;
            } else {
                $this->resetErrorBag('jumlah');
            }
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

        if ($this->jumlah > $barang->stok) {
            $this->addError(
                'jumlah',
                "Jumlah return tidak boleh melebihi stok saat ini ({$barang->stok})."
            );
            return;
        }

        try {
            DB::transaction(function () use ($barang) {
                // 1. Simpan ReturnBarang
                $return = ReturnBarang::create([
                    'id_barang'      => $this->id_barang,
                    'id_supplier'    => $barang->id_supplier,
                    'jumlah'         => $this->jumlah,
                    'alasan'         => $this->alasan,
                    'tanggal_return' => $this->tanggal_return,
                ]);

                // 2. Kurangi stok
                $barang->decrement('stok', $this->jumlah);

                // 3. Buat jurnal retur pembelian
                $this->buatJurnalReturn($return, $barang);
            });

            // 4. Reset form
            $this->resetForm();
            $this->dispatch('refreshDatatable');
            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Return barang berhasil disimpan!'
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
            $akunHutang = Akun::where('kode_akun', 'LIKE', '2.1%')->first(); // Kewajiban lancar
        }

        if (!$akunPersediaan) {
            $akunPersediaan = Akun::where('kode_akun', 'LIKE', '1.1%')->first(); // Aset lancar
        }

        if (!$akunHutang || !$akunPersediaan) {
            throw new \Exception('Akun Hutang atau Persediaan belum dikonfigurasi. Silakan hubungi administrator.');
        }

        $nilai = $barang->harga_beli * $return->jumlah; // nilai return berdasarkan harga beli
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
        $this->reset(['id_barang', 'jumlah', 'alasan']);
        $this->tanggal_return = Carbon::now()->toDateString();
        $this->resetErrorBag();
    }

    public function closeModal()
    {
        $this->resetForm();
        $this->open = false;
    }

    public function prefillFromPenjualan($data)
    {
        $this->id_barang = $data['id_barang'] ?? null;
        $this->jumlah = 1;
        $this->tanggal_return = Carbon::now()->toDateString();
        $this->open = true;
    }

    public function render()
    {
        return view('livewire.return-barang.create', [
            'listBarang' => Barang::with('supplier')
                                 ->where('stok', '>', 0)
                                 ->orderBy('nama_barang')
                                 ->get(),
            'selectedBarang' => $this->selectedBarang,
        ]);
    }
}
