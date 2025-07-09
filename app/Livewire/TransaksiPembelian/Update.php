<?php

namespace App\Livewire\TransaksiPembelian;

use Livewire\Component;
use App\Models\TransaksiPembelian;
use App\Models\Barang;
use App\Models\JurnalUmum;
use App\Models\DetailJurnal;
use App\Models\Akun;
use Illuminate\Support\Facades\DB;

class Update extends Component
{
    public $open = false;
    public $transaksiId;
    public $id_barang;
    public $tanggal_transaksi;
    public $jumlah_pembelian;
    public $total;
    public $harga_beli = 0;

    protected $listeners = ['edit' => 'loadData'];

    protected function rules()
    {
        return [
            'id_barang'         => 'required|exists:barang,id',
            'tanggal_transaksi' => 'required|date',
            'jumlah_pembelian'  => 'required|integer|min:1',
        ];
    }

    public function loadData($id)
    {
        $t = TransaksiPembelian::findOrFail($id);

        $this->transaksiId       = $t->id;
        $this->id_barang         = $t->id_barang;
        $this->tanggal_transaksi = $t->tanggal_transaksi->format('Y-m-d');
        $this->jumlah_pembelian  = $t->jumlah_pembelian;
        $this->total             = $t->total;

        if ($b = Barang::find($this->id_barang)) {
            $this->harga_beli = $b->harga_beli;
        }

        $this->open = true;
    }

    public function updatedIdBarang()
    {
        if ($b = Barang::find($this->id_barang)) {
            $this->harga_beli = $b->harga_beli;
            $this->recalculate();
        }
    }

    public function updatedJumlahPembelian()
    {
        $this->recalculate();
    }

    private function recalculate()
    {
        $this->total = $this->harga_beli * $this->jumlah_pembelian;
    }

    public function update()
{
    $this->validate();

    DB::transaction(function() {
        $t = TransaksiPembelian::findOrFail($this->transaksiId);
        $oldBarangId = $t->id_barang;
        $oldJumlah   = $t->jumlah_pembelian;

        // 1) Perbarui stok barang
        if ($oldBarangId === $this->id_barang) {
            $selisih = $this->jumlah_pembelian - $oldJumlah;
            Barang::where('id', $this->id_barang)->increment('stok', $selisih);
        } else {
            Barang::where('id', $oldBarangId)->decrement('stok', $oldJumlah);
            Barang::where('id', $this->id_barang)->increment('stok', $this->jumlah_pembelian);
        }

        // 2) Update transaksi pembelian
        $t->update([
            'id_barang'         => $this->id_barang,
            'tanggal_transaksi' => $this->tanggal_transaksi,
            'jumlah_pembelian'  => $this->jumlah_pembelian,
            'total'             => $this->total,
        ]);

        // 3) Update jurnal umum (hapus entri lama & buat baru)
        $noBukti = 'PBJ-' . $t->id;
        JurnalUmum::where('no_bukti', $noBukti)->delete();

        JurnalUmum::create([
            'id_akun'    => 105, // akun persediaan
            'tanggal'    => $this->tanggal_transaksi,
            'debit'      => $this->total,
            'kredit'     => 0,
            'keterangan' => "Pembelian {$t->barang->nama_barang}",
        ]);

        JurnalUmum::create([
            'id_akun'    => 101, // akun kas
            'tanggal'    => $this->tanggal_transaksi,
            'debit'      => 0,
            'kredit'     => $this->total,
            'keterangan' => "Pembayaran pembelian {$t->barang->nama_barang}",
        ]);
    });

    $this->reset([
        'transaksiId','id_barang','tanggal_transaksi',
        'jumlah_pembelian','total','harga_beli'
    ]);

    $this->dispatch('refreshDatatable');
    $this->open = false;
}


    public function render()
    {
        return view('livewire.transaksi-pembelian.update', [
            'listBarang' => Barang::orderBy('nama_barang')->get(),
        ]);
    }
}
