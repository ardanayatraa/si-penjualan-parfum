<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\TransaksiPenjualan;
use App\Models\Barang;
use Carbon\Carbon;

class GrafikPenjualan extends Component
{
    public $range = 'harian';
    public $tanggalSpesifik = '';
    public $tanggalAwal = '';
    public $tanggalAkhir = '';

    // Data untuk charts
    public $profitData = [];
    public $produkTerlarisData = [];

    // Chart configuration
    public $chartId = '';

    public function mount()
    {
        $this->chartId = 'chart_' . uniqid();
        $this->tanggalSpesifik = now()->format('Y-m-d');
        $this->tanggalAwal = now()->startOfWeek()->format('Y-m-d');
        $this->tanggalAkhir = now()->format('Y-m-d');

        $this->updateData();
    }

    public function updatedRange()
    {
        // Reset tanggal ketika range berubah
        match($this->range) {
            'harian' => $this->tanggalSpesifik = now()->format('Y-m-d'),
            'bulanan' => null,
            'tahunan' => null,
            'kustom' => [
                $this->tanggalAwal = now()->startOfMonth()->format('Y-m-d'),
                $this->tanggalAkhir = now()->format('Y-m-d')
            ],
            default => null
        };

        $this->updateData();
    }

    public function updatedTanggalSpesifik()
    {
        $this->updateData();
    }

    public function updatedTanggalAwal()
    {
        $this->updateData();
    }

    public function updatedTanggalAkhir()
    {
        $this->updateData();
    }

    public function lihatGrafik()
    {
        $this->updateData();
        $this->dispatch('chartDataUpdated', [
            'profitData' => $this->profitData,
            'produkData' => $this->produkTerlarisData
        ]);
    }

    /**
     * Listen to real-time updates when new transactions are created
     */
    #[On('transaksi-created')]
    #[On('transaksi-updated')]
    #[On('transaksi-deleted')]
    public function refreshData()
    {
        $this->updateData();
        $this->dispatch('chartDataUpdated', [
            'profitData' => $this->profitData,
            'produkData' => $this->produkTerlarisData
        ]);
    }

    /**
     * Update data berdasarkan filter yang dipilih
     */
    public function updateData()
    {
        $this->profitData = $this->getGrafikProfit();
        $this->produkTerlarisData = $this->getGrafikProdukTerlaris();
    }

    /**
     * Get data grafik profit
     */
    private function getGrafikProfit(): array
    {
        $query = TransaksiPenjualan::query()->where('status', 'selesai');

        if ($this->range === 'harian') {
            if ($this->tanggalSpesifik) {
                $label = Carbon::parse($this->tanggalSpesifik)->translatedFormat('d F Y');
                $total = $query->whereDate('tanggal_transaksi', $this->tanggalSpesifik)
                              ->sum('laba_bruto');
                return [['label' => $label, 'total_laba' => $total]];
            } else {
                // Seminggu terakhir
                $start = now()->startOfWeek();
                $end = now()->endOfWeek();
                $group = $query->whereBetween('tanggal_transaksi', [$start, $end])
                              ->get()
                              ->groupBy(fn($t) => Carbon::parse($t->tanggal_transaksi)->translatedFormat('l'))
                              ->map(fn($items, $day) => [
                                  'label' => $day,
                                  'total_laba' => $items->sum('laba_bruto'),
                              ]);

                $allDays = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                return collect($allDays)->map(fn($day) => [
                    'label' => $day,
                    'total_laba' => $group[$day]['total_laba'] ?? 0,
                ])->toArray();
            }
        }
        elseif ($this->range === 'bulanan') {
            $start = now()->startOfMonth();
            $end = now()->endOfMonth();
            $group = $query->whereBetween('tanggal_transaksi', [$start, $end])
                          ->get()
                          ->groupBy(fn($t) => 'Minggu ke-' . ceil(Carbon::parse($t->tanggal_transaksi)->day / 7))
                          ->map(fn($items, $week) => [
                              'label' => $week,
                              'total_laba' => $items->sum('laba_bruto'),
                          ]);

            $weeks = collect(range(1, 5))->map(fn($i) => "Minggu ke-{$i}");
            return $weeks->map(fn($week) => [
                'label' => $week,
                'total_laba' => $group[$week]['total_laba'] ?? 0,
            ])->toArray();
        }
        elseif ($this->range === 'tahunan') {
            $start = now()->subYears(4)->startOfYear();
            $end = now()->endOfYear();
            $group = $query->whereBetween('tanggal_transaksi', [$start, $end])
                          ->get()
                          ->groupBy(fn($t) => Carbon::parse($t->tanggal_transaksi)->year)
                          ->map(fn($items, $year) => [
                              'label' => (string)$year,
                              'total_laba' => $items->sum('laba_bruto'),
                          ]);

            $years = collect(range(now()->year - 4, now()->year));
            return $years->map(fn($year) => [
                'label' => (string)$year,
                'total_laba' => $group[$year]['total_laba'] ?? 0,
            ])->toArray();
        }
        elseif ($this->range === 'kustom') {
            if ($this->tanggalAwal && $this->tanggalAkhir) {
                $start = Carbon::parse($this->tanggalAwal);
                $end = Carbon::parse($this->tanggalAkhir);
                $group = $query->whereBetween('tanggal_transaksi', [$start, $end])
                              ->get()
                              ->groupBy(fn($t) => Carbon::parse($t->tanggal_transaksi)->format('d M Y'))
                              ->map(fn($items, $label) => [
                                  'label' => $label,
                                  'total_laba' => $items->sum('laba_bruto'),
                              ]);

                return $group->values()->toArray();
            }
        }

        return [];
    }

    /**
     * Get data grafik produk terlaris
     */
    private function getGrafikProdukTerlaris(): array
    {
        $end = now();
        $start = match($this->range) {
            'harian' => $this->tanggalSpesifik
                ? Carbon::parse($this->tanggalSpesifik)->startOfDay()
                : $end->copy()->startOfWeek(),
            'bulanan' => $end->copy()->startOfMonth(),
            'tahunan' => $end->copy()->startOfYear(),
            'kustom' => $this->tanggalAwal ? Carbon::parse($this->tanggalAwal) : $end->copy()->startOfMonth(),
            default => $end->copy()->startOfWeek(),
        };

        $end = match($this->range) {
            'harian' => $this->tanggalSpesifik
                ? Carbon::parse($this->tanggalSpesifik)->endOfDay()
                : $end->copy()->endOfWeek(),
            'kustom' => $this->tanggalAkhir ? Carbon::parse($this->tanggalAkhir) : $end,
            default => $end,
        };

        return TransaksiPenjualan::with('barang')
            ->where('status', 'selesai')
            ->whereBetween('tanggal_transaksi', [$start, $end])
            ->get()
            ->groupBy('id_barang')
            ->map(fn($items, $barangId) => [
                'id_barang' => $barangId,
                'label' => optional($items->first()->barang)->nama_barang ?? 'Barang Tidak Diketahui',
                'total_terjual' => $items->sum('jumlah_penjualan'),
            ])
            ->sortByDesc('total_terjual')
            ->take(5)
            ->values()
            ->toArray();
    }

    /**
     * Get summary statistics
     */
    public function getSummaryStats(): array
    {
        $totalProfit = collect($this->profitData)->sum('total_laba');
        $totalProdukTerjual = collect($this->produkTerlarisData)->sum('total_terjual');
        $avgProfit = count($this->profitData) > 0 ? $totalProfit / count($this->profitData) : 0;

        return [
            'total_profit' => $totalProfit,
            'total_produk_terjual' => $totalProdukTerjual,
            'avg_profit' => $avgProfit,
            'periode' => $this->getPeriodeText(),
        ];
    }

    /**
     * Get periode text for display
     */
    private function getPeriodeText(): string
    {
        return match($this->range) {
            'harian' => $this->tanggalSpesifik
                ? Carbon::parse($this->tanggalSpesifik)->translatedFormat('d F Y')
                : 'Minggu ini',
            'bulanan' => 'Bulan ' . now()->translatedFormat('F Y'),
            'tahunan' => 'Tahun ' . now()->year,
            'kustom' => ($this->tanggalAwal && $this->tanggalAkhir)
                ? Carbon::parse($this->tanggalAwal)->format('d/m/Y') . ' - ' . Carbon::parse($this->tanggalAkhir)->format('d/m/Y')
                : 'Periode kustom',
            default => 'Periode tidak diketahui'
        };
    }

    public function render()
    {
        $summaryStats = $this->getSummaryStats();

        return view('livewire.grafik-penjualan', [
            'summaryStats' => $summaryStats,
        ]);
    }
}
