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
        $this->updateData();
    }

    public function updateData()
    {
        $this->updateDataForToday();
    }

    // Menampilkan Data untuk 7 Hari Terakhir
    public function updateDataForToday()
    {
        $hariIni = Carbon::today();

        // === Harian ===
        $tanggalMulaiHarian = $hariIni->copy()->subDays(6);
        $harian = TransaksiPenjualan::selectRaw('DATE(tanggal_transaksi) as tanggal, SUM(laba_bersih) as total_profit')
            ->whereBetween('tanggal_transaksi', [$tanggalMulaiHarian, $hariIni])
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        $this->labelHarian = [];
        $tempHarian = [];

        for ($i = 0; $i <= 6; $i++) {
            $tgl = $tanggalMulaiHarian->copy()->addDays($i);
            $label = $tgl->translatedFormat('l');
            $tanggal = $tgl->format('Y-m-d');

            $this->labelHarian[] = $label;
            $found = $harian->firstWhere('tanggal', $tanggal);
            $tempHarian[] = $found ? (float) $found->total_profit : 0;
        }

        $this->profitHarian = $tempHarian;

        // === Mingguan ===
        $tanggalMulaiMingguan = $hariIni->copy()->startOfMonth();
        $mingguan = TransaksiPenjualan::selectRaw('WEEK(tanggal_transaksi, 3) as minggu_ke, SUM(laba_bersih) as total_profit')
            ->whereBetween('tanggal_transaksi', [$tanggalMulaiMingguan, $hariIni])
            ->groupBy('minggu_ke')
            ->orderBy('minggu_ke')
            ->get();

        $this->labelMingguan = [];
        $tempMingguan = [];

        $start = $tanggalMulaiMingguan->copy()->startOfWeek(Carbon::MONDAY);
        $end = $hariIni->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);
        $i = 1;

        while ($start <= $end) {
            $this->labelMingguan[] = "Minggu {$i}";
            $found = $mingguan->firstWhere('minggu_ke', $start->isoWeek());
            $tempMingguan[] = $found ? (float) $found->total_profit : 0;
            $start->addWeek();
            $i++;
        }

        $this->profitMingguan = $tempMingguan;

        // === Bulanan ===
        $tanggalMulaiBulanan = $hariIni->copy()->startOfYear();
        $bulanan = TransaksiPenjualan::selectRaw('MONTH(tanggal_transaksi) as bulan, SUM(laba_bersih) as total_profit')
            ->whereBetween('tanggal_transaksi', [$tanggalMulaiBulanan, $hariIni])
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        $this->labelBulanan = [];
        $tempBulanan = [];

        for ($i = 1; $i <= 12; $i++) {
            $this->labelBulanan[] = Carbon::create()->month($i)->translatedFormat('F');
            $found = $bulanan->firstWhere('bulan', $i);
            $tempBulanan[] = $found ? (float) $found->total_profit : 0;
        }

        $this->profitBulanan = $tempBulanan;
    }

    // Menampilkan Data untuk Bulan Lalu
    public function updateDataForBulanLalu()
    {
        $hariIni = Carbon::today();

        // Mendapatkan tanggal mulai dan akhir bulan lalu
        $bulanLaluMulai = $hariIni->copy()->subMonth()->startOfMonth();
        $bulanLaluAkhir = $hariIni->copy()->subMonth()->endOfMonth();

        // === Bulan Lalu ===
        $bulananLalu = TransaksiPenjualan::selectRaw('MONTH(tanggal_transaksi) as bulan, SUM(laba_bersih) as total_profit')
            ->whereBetween('tanggal_transaksi', [$bulanLaluMulai, $bulanLaluAkhir])
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        $this->labelBulanan = [];
        $this->profitBulanan = [];

        // Data bulan lalu
        $this->labelBulanan[] = $bulanLaluMulai->translatedFormat('F Y');
        $this->profitBulanan[] = $bulananLalu->sum('total_profit');

        // Produk terlaris bulan lalu
        $produkBulananLalu = TransaksiPenjualan::select('id_barang', DB::raw('SUM(jumlah) as total_terjual'))
            ->whereBetween('tanggal_transaksi', [$bulanLaluMulai, $bulanLaluAkhir])
            ->groupBy('id_barang')
            ->orderByDesc('total_terjual')
            ->take(5)
            ->get();

        $this->produkLabelBulanan = $produkBulananLalu->map(fn($item) => optional($item->barang)->nama_barang ?? 'Tidak diketahui')->toArray();
        $this->produkBulanan = $produkBulananLalu->pluck('total_terjual')->map(fn($v) => (int) $v)->toArray();
    }

    public function render()
    {
        return view('livewire.grafik-penjualan');
    }
}
