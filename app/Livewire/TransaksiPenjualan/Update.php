<?php

namespace App\Livewire\TransaksiPenjualan;

use Livewire\Component;
use App\Models\TransaksiPenjualan;
use App\Models\Barang;
use App\Models\JurnalUmum;
use App\Models\DetailJurnal;
use App\Models\Akun;
use App\Models\PajakTransaksi;
use Illuminate\Support\Facades\DB;

class Update extends Component
{
    public $open = false;
    public $id_transaksi;

    // Input
    public $id_barang;
    public $jumlah_penjualan = 1;
    public $tanggal_transaksi;
    public $id_pajak;

    // Lookup
    public $harga_jual  = 0;
    public $harga_pokok = 0;

    // Computed
    public $subtotal    = 0;
    public $laba_bruto  = 0;
    public $total_harga = 0;

    protected $listeners = ['editTransaksi' => 'loadData'];

    protected function rules()
    {
        return [
            'id_barang'        => 'required|exists:barang,id',
            'jumlah_penjualan' => 'required|integer|min:1',
            'tanggal_transaksi'=> 'required|date',
            'id_pajak'         => 'required|exists:pajak_transaksi,id',
        ];
    }

    public function loadData($id)
    {
        $t = TransaksiPenjualan::findOrFail($id);

        $this->id_transaksi      = $t->id;
        $this->id_barang         = $t->id_barang;
        $this->jumlah_penjualan  = $t->jumlah_penjualan;
        $this->tanggal_transaksi = $t->tanggal_transaksi->format('Y-m-d');
        $this->id_pajak          = $t->id_pajak;

        // lookup harga
        if ($b = Barang::find($this->id_barang)) {
            $this->harga_jual  = $b->harga_jual;
            $this->harga_pokok = $b->harga_beli;
        }

        $this->subtotal    = $t->subtotal;
        $this->laba_bruto  = $t->laba_bruto;
        $this->total_harga = $t->total_harga;

        $this->open = true;
    }

    public function updatedIdBarang($value)
    {
        if ($b = Barang::find($value)) {
            $this->harga_jual  = $b->harga_jual;
            $this->harga_pokok = $b->harga_beli;
        } else {
            $this->harga_jual = $this->harga_pokok = 0;
        }
        $this->recalculate();
    }

    public function updatedJumlahPenjualan($value)
    {
        // validasi against stok + old amount handled in update()
        $this->recalculate();
    }

    public function updatedIdPajak($value)
    {
        $this->recalculate();
    }

    private function recalculate()
    {
        $this->subtotal = $this->harga_jual * $this->jumlah_penjualan;
        $hpTotal        = $this->harga_pokok * $this->jumlah_penjualan;
        $this->laba_bruto = max(0, $this->subtotal - $hpTotal);

        if ($p = PajakTransaksi::find($this->id_pajak)) {
            $this->total_harga = round($this->subtotal * (1 + $p->presentase / 100), 2);
        } else {
            $this->total_harga = $this->subtotal;
        }
    }

    public function update()
    {
        $this->validate();

        DB::transaction(function() {
            $t = TransaksiPenjualan::findOrFail($this->id_transaksi);
            $oldBarangId  = $t->id_barang;
            $oldJumlah    = $t->jumlah_penjualan;
            $bNew         = Barang::findOrFail($this->id_barang);

            // 1) rollback & adjust stok
            if ($oldBarangId !== $this->id_barang) {
                // kembalikan stok lama
                Barang::where('id', $oldBarangId)->increment('stok', $oldJumlah);
                // kurangi stok barang baru
                $bNew->decrement('stok', $this->jumlah_penjualan);
            } else {
                // same barang: adjust selisih
                $selisih = $this->jumlah_penjualan - $oldJumlah;
                if ($selisih > 0) {
                    $bNew->decrement('stok', $selisih);
                } elseif ($selisih < 0) {
                    $bNew->increment('stok', abs($selisih));
                }
            }

            // 2) update transaksi
            $t->update([
                'id_barang'         => $this->id_barang,
                'jumlah_penjualan'  => $this->jumlah_penjualan,
                'tanggal_transaksi' => $this->tanggal_transaksi,
                'id_pajak'          => $this->id_pajak,
                'subtotal'          => $this->subtotal,
                'harga_pokok'       => $this->harga_pokok * $this->jumlah_penjualan,
                'laba_bruto'        => $this->laba_bruto,
                'total_harga'       => $this->total_harga,
            ]);

            // 3) update jurnal umum header
            $noBukti = 'PNJ-' . $t->id;
            $j = JurnalUmum::where('no_bukti', $noBukti)->firstOrFail();
            $j->update([
                'tanggal'    => $this->tanggal_transaksi,
                'keterangan' => "Penjualan {$bNew->nama_barang}",
            ]);

            // 4) hapus detail lama & tulis ulang
            $j->detailJurnal()->delete();

            // Debit Kas/Bank (1102)
            $akunKas = Akun::where('kode_akun','1102')->firstOrFail();
            DetailJurnal::create([
                'jurnal_umum_id' => $j->id,
                'akun_id'        => $akunKas->id,
                'debit'          => $this->total_harga,
                'kredit'         => 0,
            ]);

            // Kredit Pendapatan (4001)
            $akunPdpt = Akun::where('kode_akun','4001')->firstOrFail();
            DetailJurnal::create([
                'jurnal_umum_id' => $j->id,
                'akun_id'        => $akunPdpt->id,
                'debit'          => 0,
                'kredit'         => $this->subtotal,
            ]);

            // Kredit Pajak Keluaran (2102)
            $pajakAmt = $this->total_harga - $this->subtotal;
            if ($pajakAmt > 0) {
                $akunPjk = Akun::where('kode_akun','2102')->firstOrFail();
                DetailJurnal::create([
                    'jurnal_umum_id' => $j->id,
                    'akun_id'        => $akunPjk->id,
                    'debit'          => 0,
                    'kredit'         => $pajakAmt,
                ]);
            }
        });

        // reset & refresh
        $this->reset([
            'id_transaksi','id_barang','jumlah_penjualan',
            'tanggal_transaksi','id_pajak','harga_jual',
            'harga_pokok','subtotal','laba_bruto','total_harga',
        ]);
        $this->dispatch('refreshDatatable');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.transaksi-penjualan.update', [
            'listBarang' => Barang::orderBy('nama_barang')->get(),
            'listPajak'  => PajakTransaksi::orderBy('presentase')->get(),
        ]);
    }
}
