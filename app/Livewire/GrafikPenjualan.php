<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\TransaksiPenjualan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GrafikPenjualan extends Component
{
    public $labelHarian = [], $profitHarian = [];
    public $labelMingguan = [], $profitMingguan = [];
    public $labelBulanan = [], $profitBulanan = [];
    public $produkLabelHarian = [], $produkHarian = [];
    public $produkLabelMingguan = [], $produkMingguan = [];
    public $produkLabelBulanan = [], $produkBulanan = [];

    public function mount()
    {
        $this->updateDataForToday();
    }

    public function updateDataForToday()
    {
        $today = Carbon::today();
        // Harian
        $start = $today->copy()->subDays(6);
        $rows = TransaksiPenjualan::selectRaw('DATE(tanggal_transaksi) as tgl, SUM(laba_bruto) as tot')
            ->whereBetween('tanggal_transaksi', [$start, $today])
            ->groupBy('tgl')
            ->orderBy('tgl')
            ->get();

        $this->labelHarian = [];
        $tmp = [];
        for ($i = 0; $i <= 6; $i++) {
            $d = $start->copy()->addDays($i);
            $this->labelHarian[] = $d->translatedFormat('l');
            $row = $rows->firstWhere('tgl', $d->format('Y-m-d'));
            $tmp[] = $row ? (float)$row->tot : 0;
        }
        $this->profitHarian = $tmp;

        // Mingguan (bulan ini)
        $mStart = $today->copy()->startOfMonth();
        $mRows = TransaksiPenjualan::selectRaw('WEEK(tanggal_transaksi,3) as wk, SUM(laba_bruto) as tot')
            ->whereBetween('tanggal_transaksi', [$mStart, $today])
            ->groupBy('wk')->orderBy('wk')->get();
        $this->labelMingguan = []; $tmp = [];
        $w = 1;
        $wkStart = $mStart->copy()->startOfWeek();
        while ($wkStart <= $today) {
            $this->labelMingguan[] = "Minggu {$w}";
            $r = $mRows->firstWhere('wk', $wkStart->isoWeek());
            $tmp[] = $r ? (float)$r->tot : 0;
            $wkStart->addWeek(); $w++;
        }
        $this->profitMingguan = $tmp;

        // Bulanan (tahun ini)
        $yStart = $today->copy()->startOfYear();
        $yRows = TransaksiPenjualan::selectRaw('MONTH(tanggal_transaksi) as bln, SUM(laba_bruto) as tot')
            ->whereBetween('tanggal_transaksi', [$yStart, $today])
            ->groupBy('bln')->get();
        $this->labelBulanan=[]; $tmp=[];
        for ($m=1;$m<=12;$m++){
            $this->labelBulanan[] = Carbon::create()->month($m)->translatedFormat('F');
            $r = $yRows->firstWhere('bln',$m);
            $tmp[] = $r ? (float)$r->tot : 0;
        }
        $this->profitBulanan = $tmp;

        // Produk terlaris harian
        $pRows = TransaksiPenjualan::select('id_barang', DB::raw('SUM(jumlah_penjualan) as tot'))
            ->whereBetween('tanggal_transaksi', [$start, $today])
            ->groupBy('id_barang')->orderByDesc('tot')->take(5)->get();
        $this->produkLabelHarian = $pRows->map(fn($i) => optional($i->barang)->nama_barang)->toArray();
        $this->produkHarian = $pRows->pluck('tot')->map(fn($v)=>(int)$v)->toArray();

        // Produk terlaris mingguan
        $wkStart = $mStart->copy()->startOfWeek();
        $pRows = TransaksiPenjualan::select('id_barang', DB::raw('SUM(jumlah_penjualan) as tot'))
            ->whereBetween('tanggal_transaksi', [$wkStart, $today])
            ->groupBy('id_barang')->orderByDesc('tot')->take(5)->get();
        $this->produkLabelMingguan = $pRows->map(fn($i)=>optional($i->barang)->nama_barang)->toArray();
        $this->produkMingguan = $pRows->pluck('tot')->map(fn($v)=>(int)$v)->toArray();

        // Produk terlaris bulanan
        $bStart = $today->copy()->startOfMonth();
        $pRows = TransaksiPenjualan::select('id_barang', DB::raw('SUM(jumlah_penjualan) as tot'))
            ->whereBetween('tanggal_transaksi', [$bStart, $today])
            ->groupBy('id_barang')->orderByDesc('tot')->take(5)->get();
        $this->produkLabelBulanan = $pRows->map(fn($i)=>optional($i->barang)->nama_barang)->toArray();
        $this->produkBulanan = $pRows->pluck('tot')->map(fn($v)=>(int)$v)->toArray();
    }

    public function render()
    {
        return view('livewire.grafik-penjualan');
    }
}
