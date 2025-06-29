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
use Illuminate\Support\Carbon;

class Create extends Component
{
    public $open = false;

    public $id_barang;
    public $jumlah_penjualan = 1;
    public $tanggal_transaksi;
    public $id_pajak;

    public $harga_jual = 0;
    public $total_harga = 0;

    protected $rules = [
        'id_barang'        => 'required|exists:barang,id',
        'jumlah_penjualan' => 'required|integer|min:1',
        'tanggal_transaksi'=> 'required|date',
    ];

    public function mount()
    {
        $this->tanggal_transaksi = Carbon::now()->toDateString();
        $this->id_pajak = PajakTransaksi::find(1)?->id ?? null;
    }

    public function updatedIdBarang($value)
    {
        $b = Barang::find($value);
        if ($b) {
            $this->harga_jual = $b->harga_jual;
            if ($this->jumlah_penjualan > $b->stok) {
                $this->jumlah_penjualan = $b->stok;
            }
        } else {
            $this->harga_jual = 0;
        }
        $this->recalculate();
    }

    public function updatedJumlahPenjualan($value)
    {
        $b = Barang::find($this->id_barang);
        if ($b && $value > $b->stok) {
            $this->addError('jumlah_penjualan', "Jumlah penjualan tidak boleh melebihi stok ({$b->stok}).");
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
        $subtotal = $this->harga_jual * $this->jumlah_penjualan;
        if ($p = PajakTransaksi::find($this->id_pajak)) {
            $this->total_harga = round($subtotal * (1 + $p->presentase / 100), 2);
        } else {
            $this->total_harga = $subtotal;
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
            $this->addError('jumlah_penjualan', "Jumlah melebihi stok ({$barang->stok}).");
            return;
        }

        DB::transaction(function () use ($barang) {
            $barang->decrement('stok', $this->jumlah_penjualan);

            $harga_jual_total = $this->harga_jual * $this->jumlah_penjualan;

            $t = TransaksiPenjualan::create([
                'id_kasir'         => Auth::id(),
                'id_barang'        => $this->id_barang,
                'jumlah_penjualan' => $this->jumlah_penjualan,
                'tanggal_transaksi'=> $this->tanggal_transaksi,
                'id_pajak'         => $this->id_pajak,
                'harga_pokok'      => $barang->harga_jual,
                'subtotal'       => $harga_jual_total,
                'total_harga'      => $this->total_harga,
            ]);

            $j = JurnalUmum::create([
                'tanggal'    => $this->tanggal_transaksi,
                'no_bukti'   => 'PNJ-'.$t->id,
                'keterangan' => "Penjualan {$barang->nama_barang}",
            ]);

            $akunKas = Akun::where('kode_akun', '1.1.01')->firstOrFail();
            DetailJurnal::create([
                'jurnal_umum_id' => $j->id,
                'akun_id'        => $akunKas->id,
                'debit'          => $this->total_harga,
                'kredit'         => 0,
            ]);

            $akunPenjualan = Akun::where('kode_akun', '4.1.01')->firstOrFail();
            DetailJurnal::create([
                'jurnal_umum_id' => $j->id,
                'akun_id'        => $akunPenjualan->id,
                'debit'          => 0,
                'kredit'         => $harga_jual_total,
            ]);

            $pajakAmt = $this->total_harga - $harga_jual_total;
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

        $this->reset([
            'id_barang', 'jumlah_penjualan', 'tanggal_transaksi',
            'id_pajak', 'harga_jual', 'total_harga',
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
