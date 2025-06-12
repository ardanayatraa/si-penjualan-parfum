<?php

namespace App\Livewire\TransaksiPenjualan;

use Livewire\Component;
use App\Models\TransaksiPenjualan;
use App\Models\Barang;
use App\Models\PajakTransaksi;
use App\Models\JurnalUmum;
use App\Models\DetailJurnal;
use App\Models\Akun;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Create extends Component
{
    public $open = false;

    // Input fields
    public $id_barang;
    public $jumlah_penjualan = 1;
    public $tanggal_transaksi;
    public $id_pajak;

    // Lookup values
    public $harga_jual  = 0;
    public $harga_pokok = 0;

    // Computed
    public $subtotal    = 0;
    public $laba_bruto  = 0;
    public $total_harga = 0;

    protected $rules = [
        'id_barang'        => 'required|exists:barang,id',
        'jumlah_penjualan' => 'required|integer|min:1',
        'tanggal_transaksi'=> 'required|date',
        'id_pajak'         => 'required|exists:pajak_transaksi,id',
    ];

    public function updatedIdBarang($value)
    {
        $b = Barang::find($value);
        if ($b) {
            $this->harga_jual  = $b->harga_jual;
            $this->harga_pokok = $b->harga_beli;
            if ($this->jumlah_penjualan > $b->stok) {
                $this->jumlah_penjualan = $b->stok;
            }
        } else {
            $this->harga_jual = $this->harga_pokok = 0;
        }
        $this->recalculate();
    }

    public function updatedJumlahPenjualan($value)
    {
        $b = Barang::find($this->id_barang);
        if ($b && $value > $b->stok) {
            $this->addError(
                'jumlah_penjualan',
                "Jumlah penjualan tidak boleh melebihi stok ({$b->stok})."
            );
            $this->jumlah_penjualan = $b->stok;
        } else {
            $this->resetErrorBag('jumlah_penjualan');
        }
        $this->recalculate();
    }

    public function updatedIdPajak($value)
    {
        $this->recalculate();
    }

    private function recalculate()
    {
        $this->subtotal   = $this->harga_jual * $this->jumlah_penjualan;
        $hpTotal         = $this->harga_pokok * $this->jumlah_penjualan;
        $this->laba_bruto = max(0, $this->subtotal - $hpTotal);

        if ($p = PajakTransaksi::find($this->id_pajak)) {
            $this->total_harga = round($this->subtotal * (1 + $p->presentase/100), 2);
        } else {
            $this->total_harga = $this->subtotal;
        }
    }

    public function store()
    {
        $this->validate();

        $barang = Barang::find($this->id_barang);
        if (! $barang) {
            $this->addError('id_barang', 'Barang tidak ditemukan.');
            return;
        }
        if ($this->jumlah_penjualan > $barang->stok) {
            $this->addError(
                'jumlah_penjualan',
                "Jumlah melebihi stok ({$barang->stok})."
            );
            return;
        }

        DB::transaction(function() use ($barang) {
            // 1) kurangi stok
            $barang->decrement('stok', $this->jumlah_penjualan);

            // 2) simpan transaksi
            $t = TransaksiPenjualan::create([
                'id_kasir'           => Auth::id(),
                'id_barang'          => $this->id_barang,
                'jumlah_penjualan'   => $this->jumlah_penjualan,
                'tanggal_transaksi'  => $this->tanggal_transaksi,
                'id_pajak'           => $this->id_pajak,
                'subtotal'           => $this->subtotal,
                'harga_pokok'        => $this->harga_pokok * $this->jumlah_penjualan,
                'laba_bruto'         => $this->laba_bruto,
                'total_harga'        => $this->total_harga,
            ]);

            // 3) header jurnal
            $j = JurnalUmum::create([
                'tanggal'    => $this->tanggal_transaksi,
                'no_bukti'   => 'PNJ-'.$t->id,
                'keterangan' => "Penjualan {$barang->nama_barang}",
            ]);

            // 4) Debit Kas (akun kode 1.1.01)
            $akunKas = Akun::where('kode_akun', '1.1.01')->firstOrFail();
            DetailJurnal::create([
                'jurnal_umum_id' => $j->id,
                'akun_id'        => $akunKas->id,
                'debit'          => $this->total_harga,
                'kredit'         => 0,
            ]);

            // 5) Kredit Penjualan Barang (akun kode 4.1.01) = subtotal
            $akunPenjualan = Akun::where('kode_akun', '4.1.01')->firstOrFail();
            DetailJurnal::create([
                'jurnal_umum_id' => $j->id,
                'akun_id'        => $akunPenjualan->id,
                'debit'          => 0,
                'kredit'         => $this->subtotal,
            ]);

            // 6) Kredit PPN Keluaran (akun kode 2.1.02) = selisih pajak
            $pajakAmt = $this->total_harga - $this->subtotal;
            if ($pajakAmt > 0) {
                $akunPpn = Akun::where('kode_akun', '2.1.02')->firstOrFail();
                DetailJurnal::create([
                    'jurnal_umum_id' => $j->id,
                    'akun_id'        => $akunPpn->id,
                    'debit'          => 0,
                    'kredit'         => $pajakAmt,
                ]);
            }
        });

        // reset form
        $this->reset([
            'id_barang', 'jumlah_penjualan', 'tanggal_transaksi',
            'id_pajak', 'harga_jual', 'harga_pokok',
            'subtotal', 'laba_bruto', 'total_harga',
        ]);

        $this->dispatch('refreshDatatable');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.transaksi-penjualan.create', [
            'listBarang' => Barang::all(),
            'listPajak'  => PajakTransaksi::all(),
        ]);
    }
}
