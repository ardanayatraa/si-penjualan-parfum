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

    public $id_barang;
    public $jumlah_penjualan = 1;
    public $tanggal_transaksi;
    public $id_pajak;

    public $harga_jual = 0;
    public $total_harga = 0;

    protected $listeners = ['edit' => 'loadData'];

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
        $t = TransaksiPenjualan::find($id);
        if (!$t) {
            $this->dispatch('showError', message: 'Transaksi tidak ditemukan');
            return;
        }

        $this->id_transaksi      = $t->id;
        $this->id_barang         = $t->id_barang;
        $this->jumlah_penjualan  = $t->jumlah_penjualan;
        $this->tanggal_transaksi = $t->tanggal_transaksi->format('Y-m-d');
        $this->id_pajak          = $t->id_pajak;

        if ($b = Barang::find($this->id_barang)) {
            $this->harga_jual = $b->harga_jual;
        }

        $this->total_harga = $t->total_harga;

        $this->open = true;
    }

    public function updatedIdBarang($value)
    {
        if ($b = Barang::find($value)) {
            $this->harga_jual = $b->harga_jual;
        } else {
            $this->harga_jual = 0;
        }
        $this->recalculate();
    }

    public function updatedJumlahPenjualan($value)
    {
        $this->recalculate();
    }

    public function updatedIdPajak($value)
    {
        $this->recalculate();
    }

    private function recalculate()
    {
        $harga_jual_total = $this->harga_jual * $this->jumlah_penjualan;

        if ($p = PajakTransaksi::find($this->id_pajak)) {
            $this->total_harga = round($harga_jual_total * (1 + $p->presentase / 100), 2);
        } else {
            $this->total_harga = $harga_jual_total;
        }
    }

    public function update()
    {
        $this->validate();

        DB::transaction(function () {
            $t = TransaksiPenjualan::find($this->id_transaksi);
            if (!$t) {
                $this->dispatch('showError', message: 'Transaksi tidak ditemukan');
                return;
            }

            $oldBarangId  = $t->id_barang;
            $oldJumlah    = $t->jumlah_penjualan;
            $bNew         = Barang::find($this->id_barang);
            if (!$bNew) {
                $this->dispatch('showError', message: 'Barang tidak ditemukan');
                return;
            }

            if ($oldBarangId !== $this->id_barang) {
                Barang::where('id', $oldBarangId)->increment('stok', $oldJumlah);
                $bNew->decrement('stok', $this->jumlah_penjualan);
            } else {
                $selisih = $this->jumlah_penjualan - $oldJumlah;
                if ($selisih > 0) {
                    $bNew->decrement('stok', $selisih);
                } elseif ($selisih < 0) {
                    $bNew->increment('stok', abs($selisih));
                }
            }

            $harga_jual_total = $this->harga_jual * $this->jumlah_penjualan;

            $t->update([
                'id_barang'         => $this->id_barang,
                'jumlah_penjualan'  => $this->jumlah_penjualan,
                'tanggal_transaksi' => $this->tanggal_transaksi,
                'id_pajak'          => $this->id_pajak,
                'harga_jual'        => $harga_jual_total,
                'total_harga'       => $this->total_harga,
            ]);

            $noBukti = 'PNJ-' . $t->id;
            $j = JurnalUmum::where('no_bukti', $noBukti)->first();
            if (!$j) {
                $this->dispatch('showError', message: 'Jurnal Umum tidak ditemukan');
                return;
            }

            $j->update([
                'tanggal'    => $this->tanggal_transaksi,
                'keterangan' => "Penjualan {$bNew->nama_barang}",
            ]);

            $j->detailJurnal()->delete();

            $akunKas = Akun::where('kode_akun','1.1.01')->first();
            $akunPdpt = Akun::where('kode_akun','4.1.01')->first();
            $akunPjk = Akun::where('kode_akun','2.1.02')->first();

            if ($akunKas) {
                DetailJurnal::create([
                    'jurnal_umum_id' => $j->id,
                    'akun_id'        => $akunKas->id,
                    'debit'          => $this->total_harga,
                    'kredit'         => 0,
                ]);
            }

            if ($akunPdpt) {
                DetailJurnal::create([
                    'jurnal_umum_id' => $j->id,
                    'akun_id'        => $akunPdpt->id,
                    'debit'          => 0,
                    'kredit'         => $harga_jual_total,
                ]);
            }

            $pajakAmt = $this->total_harga - $harga_jual_total;
            if ($pajakAmt > 0 && $akunPjk) {
                DetailJurnal::create([
                    'jurnal_umum_id' => $j->id,
                    'akun_id'        => $akunPjk->id,
                    'debit'          => 0,
                    'kredit'         => $pajakAmt,
                ]);
            }
        });

        $this->reset([
            'id_transaksi','id_barang','jumlah_penjualan',
            'tanggal_transaksi','id_pajak','harga_jual','total_harga',
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
