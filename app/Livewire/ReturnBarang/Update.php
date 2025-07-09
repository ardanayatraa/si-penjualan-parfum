<?php

namespace App\Livewire\ReturnBarang;

use Livewire\Component;
use App\Models\ReturnBarang;
use App\Models\Barang;
use App\Models\JurnalUmum;
use App\Models\Akun;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Update extends Component
{
    public $open = false;
    public $returnId;
    public $id_barang;
    public $jumlah;
    public $alasan;
    public $tanggal_return;

    // untuk validasi stok saat edit
    public $originalJumlah = 0;
    public $availableStok = 0;
    public $originalData = [];

    protected $listeners = ['edit' => 'loadData'];

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

    public function loadData($id)
    {
        $return = ReturnBarang::findOrFail($id);

        // Store original data
        $this->originalData = [
            'id_barang' => $return->id_barang,
            'jumlah' => $return->jumlah,
        ];

        $this->returnId = $return->id;
        $this->id_barang = $return->id_barang;
        $this->jumlah = $return->jumlah;
        $this->alasan = $return->alasan;
        $this->tanggal_return = $return->tanggal_return;
        $this->originalJumlah = $return->jumlah;

        // hitung stok tersedia: stok sekarang + jumlah retur lama
        $barang = Barang::find($this->id_barang);
        $this->availableStok = $barang
            ? $barang->stok + $this->originalJumlah
            : $this->originalJumlah;

        $this->open = true;
    }

    public function updatedIdBarang($value)
    {
        if ($value && $value !== $this->originalData['id_barang']) {
            // Jika ganti barang, reset jumlah dan hitung ulang stok tersedia
            $barang = Barang::find($value);
            $this->availableStok = $barang ? $barang->stok : 0;
            $this->jumlah = min($this->jumlah, $this->availableStok);
        } elseif ($value === $this->originalData['id_barang']) {
            // Jika barang sama dengan aslinya, tambahkan original jumlah
            $barang = Barang::find($value);
            $this->availableStok = $barang ? $barang->stok + $this->originalJumlah : $this->originalJumlah;
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

    public function update()
    {
        $this->validate();

        $returnOld = ReturnBarang::findOrFail($this->returnId);
        $barang = Barang::find($this->id_barang);

        if (!$barang) {
            $this->addError('id_barang', 'Barang tidak ditemukan.');
            return;
        }

        // cek stok sekali lagi
        $maxAllow = $this->id_barang === $this->originalData['id_barang']
            ? $barang->stok + $returnOld->jumlah
            : $barang->stok;

        if ($this->jumlah > $maxAllow) {
            $this->addError(
                'jumlah',
                "Jumlah return tidak boleh melebihi stok tersedia ({$maxAllow})."
            );
            return;
        }

        try {
            DB::transaction(function () use ($returnOld, $barang) {
                // 1. Hapus jurnal lama
                $this->hapusJurnalLama($returnOld);

                // 2. Adjust stok
                $this->adjustStok($returnOld, $barang);

                // 3. Update record retur
                $returnOld->update([
                    'id_barang'      => $this->id_barang,
                    'jumlah'         => $this->jumlah,
                    'alasan'         => $this->alasan,
                    'tanggal_return' => $this->tanggal_return,
                ]);

                // 4. Buat jurnal retur baru
                $this->buatJurnalReturn($returnOld->refresh(), $barang);
            });

            $this->resetForm();
            $this->dispatch('refreshDatatable');
            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Return barang berhasil diupdate!'
            ]);
            $this->open = false;

        } catch (\Exception $e) {
            $this->addError('general', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    protected function hapusJurnalLama($returnOld)
    {
        // Hapus jurnal berdasarkan keterangan yang mengandung ID return
        JurnalUmum::where('keterangan', 'LIKE', "%Return ID {$returnOld->id}%")->delete();
    }

    protected function adjustStok($returnOld, $barangBaru)
    {
        $barangLama = Barang::find($this->originalData['id_barang']);

        if ($this->originalData['id_barang'] === $this->id_barang) {
            // Barang sama, adjust berdasarkan selisih jumlah
            $selisih = $this->jumlah - $this->originalData['jumlah'];
            if ($selisih > 0) {
                // Jumlah bertambah, kurangi stok lebih banyak
                $barangBaru->decrement('stok', $selisih);
            } elseif ($selisih < 0) {
                // Jumlah berkurang, kembalikan stok
                $barangBaru->increment('stok', abs($selisih));
            }
        } else {
            // Barang berbeda
            // Kembalikan stok barang lama
            if ($barangLama) {
                $barangLama->increment('stok', $this->originalData['jumlah']);
            }
            // Kurangi stok barang baru
            $barangBaru->decrement('stok', $this->jumlah);
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
        $keterangan = "Update Return barang {$barang->nama_barang} - Return ID {$return->id}";

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
            'returnId',
            'id_barang',
            'jumlah',
            'alasan',
            'tanggal_return',
            'originalJumlah',
            'availableStok',
            'originalData',
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
        return view('livewire.return-barang.update', [
            'listBarang' => Barang::with('supplier')
                                 ->orderBy('nama_barang')
                                 ->get(),
            'selectedBarang' => $this->selectedBarang,
        ]);
    }
}
