<?php

namespace App\Livewire\ReturnBarang;

use Livewire\Component;
use App\Models\ReturnBarang;
use App\Models\Barang;
use App\Models\JurnalUmum;
use App\Models\DetailJurnal;
use App\Models\Akun;
use Carbon\Carbon;

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
    public $availableStok  = 0;

    protected $listeners = ['edit' => 'loadData'];

    protected $rules = [
        'id_barang'       => 'required|exists:barang,id',
        'jumlah'          => 'required|integer|min:1',
        'alasan'          => 'required|string|max:500',
        'tanggal_return'  => 'required|date',
    ];

    public function loadData($id)
    {
        $r = ReturnBarang::findOrFail($id);

        $this->returnId       = $r->id;
        $this->id_barang      = $r->id_barang;
        $this->jumlah         = $r->jumlah;
        $this->alasan         = $r->alasan;
        $this->tanggal_return = $r->tanggal_return;
        $this->originalJumlah = $r->jumlah;

        // hitung stok tersedia: stok sekarang + jumlah retur lama
        $b = Barang::find($this->id_barang);
        $this->availableStok = $b
            ? $b->stok + $this->originalJumlah
            : $this->originalJumlah;

        $this->open = true;
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

        $rOld   = ReturnBarang::findOrFail($this->returnId);
        $barang = Barang::find($this->id_barang);

        if (! $barang) {
            $this->addError('id_barang', 'Barang tidak ditemukan.');
            return;
        }

        // cek stok sekali lagi
        $maxAllow = $barang->stok + $rOld->jumlah;
        if ($this->jumlah > $maxAllow) {
            $this->addError(
                'jumlah',
                "Jumlah return tidak boleh melebihi stok tersedia ({$maxAllow})."
            );
            return;
        }

        // ----- 1) Hapus jurnal lama -----
        if ($rOld->jurnal_umum_id) {
            DetailJurnal::where('jurnal_umum_id', $rOld->jurnal_umum_id)->delete();
            JurnalUmum::where('id', $rOld->jurnal_umum_id)->delete();
        }

        // ----- 2) Kembalikan stok sesuai retur lama, lalu kurangi stok sesuai retur baru -----
        $barang->increment('stok', $rOld->jumlah);
        $barang->decrement('stok', $this->jumlah);

        // ----- 3) Update record retur -----
        $rOld->update([
            'id_barang'      => $this->id_barang,
            'jumlah'         => $this->jumlah,
            'alasan'         => $this->alasan,
            'tanggal_return' => $this->tanggal_return,
        ]);

        // ----- 4) Buat jurnal retur baru -----
        $this->buatJurnalReturn($rOld->refresh(), $barang);

        // ----- 5) Reset state & notifikasi -----
        $this->reset([
            'returnId',
            'id_barang',
            'jumlah',
            'alasan',
            'tanggal_return',
            'originalJumlah',
            'availableStok',
        ]);

        $this->dispatch('refreshDatatable');
        $this->open = false;
    }

    protected function buatJurnalReturn(ReturnBarang $return, Barang $barang)
    {
        // format no_bukti: JR-RB-YYYYMMDD-XXX
        $tgl   = Carbon::parse($return->tanggal_return)->format('Ymd');
        $urut  = JurnalUmum::whereDate('tanggal', $return->tanggal_return)->count() + 1;
        $noBukti = sprintf('JR-RB-%s-%03d', $tgl, $urut);

        // buat header jurnal
        $j = JurnalUmum::create([
            'tanggal'    => $return->tanggal_return,
            'no_bukti'   => $noBukti,
            'keterangan' => 'Retur Barang ID ' . $return->id,
        ]);

        // ambil akun-akun
        $akunHutang     = Akun::where('kode_akun','2.1.01')->first(); // Hutang Dagang
        $akunPersediaan = Akun::where('kode_akun','1.1.05')->first(); // Persediaan Barang

        // nilai pokok retur
        $nilai = $barang->harga_beli * $return->jumlah;

        // debit Hutang Dagang (mengurangi hutang)
        if ($akunHutang) {
            DetailJurnal::create([
                'jurnal_umum_id' => $j->id,
                'akun_id'        => $akunHutang->id,
                'debit'          => $nilai,
                'kredit'         => 0,
            ]);
        }

        // kredit Persediaan Barang (kembalikan stok)
        if ($akunPersediaan) {
            DetailJurnal::create([
                'jurnal_umum_id' => $j->id,
                'akun_id'        => $akunPersediaan->id,
                'debit'          => 0,
                'kredit'         => $nilai,
            ]);
        }

        // update referensi jurnal di return_barang
        $return->update(['jurnal_umum_id' => $j->id]);
    }

    public function render()
    {
        return view('livewire.return-barang.update', [
            'listBarang' => Barang::all(),
        ]);
    }
}
