<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\TransaksiPenjualan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GrafikPenjualan extends Component
{
    // Labels & data untuk profit
    public $labelHarian    = [], $profitHarian    = [];
    public $labelMingguan  = [], $profitMingguan  = [];
    public $labelBulanan   = [], $profitBulanan   = [];

    // Labels & data untuk produk terlaris
    public $produkLabelHarian    = [], $produkHarian    = [];
    public $produkLabelMingguan  = [], $produkMingguan  = [];
    public $produkLabelBulanan   = [], $produkBulanan   = [];

    public function mount()
    {
        $this->updateDataForToday();
    }

    public function updateDataForToday()
    {
        $today = Carbon::today();

        //
        // === 1) PROFIT ===
        //

        // a) Profit Harian (7 hari terakhir)
        $start = $today->copy()->subDays(6);
        $rows = TransaksiPenjualan::selectRaw('DATE(tanggal_transaksi) as tgl, SUM(laba_bruto) as tot')
            ->whereBetween('tanggal_transaksi', [$start, $today])
            ->groupBy('tgl')
            ->orderBy('tgl')
            ->get();

        $this->labelHarian = [];
        $tmpProfit = [];
        for ($i = 0; $i <= 6; $i++) {
            $d = $start->copy()->addDays($i);
            $this->labelHarian[] = $d->translatedFormat('l'); // hari dalam bahasa lokal
            $row = $rows->firstWhere('tgl', $d->format('Y-m-d'));
            $tmpProfit[] = $row ? (float)$row->tot : 0;
        }
        $this->profitHarian = $tmpProfit;

        // b) Profit Mingguan (bulan ini, per minggu)
        $mStart = $today->copy()->startOfMonth();
        $mRows = TransaksiPenjualan::selectRaw('WEEK(tanggal_transaksi, 3) as wk, SUM(laba_bruto) as tot')
            ->whereBetween('tanggal_transaksi', [$mStart, $today])
            ->groupBy('wk')
            ->orderBy('wk')
            ->get();

        $this->labelMingguan = [];
        $tmpProfit = [];
        $weekNumber = 1;
        $wkCursor = $mStart->copy()->startOfWeek();
        while ($wkCursor <= $today) {
            $this->labelMingguan[] = "Minggu {$weekNumber}";
            $r = $mRows->firstWhere('wk', $wkCursor->isoWeek());
            $tmpProfit[] = $r ? (float)$r->tot : 0;
            $wkCursor->addWeek();
            $weekNumber++;
        }
        $this->profitMingguan = $tmpProfit;

        // c) Profit Bulanan (tahun ini, per bulan)
        $yStart = $today->copy()->startOfYear();
        $yRows = TransaksiPenjualan::selectRaw('MONTH(tanggal_transaksi) as bln, SUM(laba_bruto) as tot')
            ->whereBetween('tanggal_transaksi', [$yStart, $today])
            ->groupBy('bln')
            ->get();

        $this->labelBulanan = [];
        $tmpProfit = [];
        for ($m = 1; $m <= 12; $m++) {
            $this->labelBulanan[] = Carbon::create()->month($m)->translatedFormat('F');
            $r = $yRows->firstWhere('bln', $m);
            $tmpProfit[] = $r ? (float)$r->tot : 0;
        }
        $this->profitBulanan = $tmpProfit;

        //
        // === 2) PRODUK TERLARIS (GROUP VARIAN) ===
        //
        // Kita anggap `barang.nama_barang` disimpan seperti "Parfum X – Varian A"
        // dan delimiter antar varian adalah " – ". Fungsi SUBSTRING_INDEX akan
        // mengambil bagian sebelum " – " sebagai nama produk utama.

        // a) Produk Terlaris Harian (7 hari terakhir)
        $pRows = TransaksiPenjualan::selectRaw("
                SUBSTRING_INDEX(barang.nama_barang, ' – ', 1) AS nama_produk_utama,
                SUM(jumlah_penjualan) AS tot
            ")
            ->join('barang', 'transaksi_penjualan.id_barang', '=', 'barang.id')
            ->whereBetween('tanggal_transaksi', [$start, $today])
            ->groupBy('nama_produk_utama')
            ->orderByDesc('tot')
            ->take(5)
            ->get();

        $this->produkLabelHarian = $pRows->pluck('nama_produk_utama')->toArray();
        $this->produkHarian      = $pRows->pluck('tot')->map(fn($v) => (int)$v)->toArray();

        // b) Produk Terlaris Mingguan (mulai minggu ini)
        $wkStart = $mStart->copy()->startOfWeek();
        $pRows = TransaksiPenjualan::selectRaw("
                SUBSTRING_INDEX(barang.nama_barang, ' – ', 1) AS nama_produk_utama,
                SUM(jumlah_penjualan) AS tot
            ")
            ->join('barang', 'transaksi_penjualan.id_barang', '=', 'barang.id')
            ->whereBetween('tanggal_transaksi', [$wkStart, $today])
            ->groupBy('nama_produk_utama')
            ->orderByDesc('tot')
            ->take(5)
            ->get();

        $this->produkLabelMingguan = $pRows->pluck('nama_produk_utama')->toArray();
        $this->produkMingguan      = $pRows->pluck('tot')->map(fn($v) => (int)$v)->toArray();

        // c) Produk Terlaris Bulanan (dari tgl 1 bulan ini)
        $bStart = $today->copy()->startOfMonth();
        $pRows = TransaksiPenjualan::selectRaw("
                SUBSTRING_INDEX(barang.nama_barang, ' – ', 1) AS nama_produk_utama,
                SUM(jumlah_penjualan) AS tot
            ")
            ->join('barang', 'transaksi_penjualan.id_barang', '=', 'barang.id')
            ->whereBetween('tanggal_transaksi', [$bStart, $today])
            ->groupBy('nama_produk_utama')
            ->orderByDesc('tot')
            ->take(5)
            ->get();

        $this->produkLabelBulanan = $pRows->pluck('nama_produk_utama')->toArray();
        $this->produkBulanan      = $pRows->pluck('tot')->map(fn($v) => (int)$v)->toArray();
    }

    public function render()
    {
        return view('livewire.grafik-penjualan');
    }
}
