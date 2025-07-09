<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransaksiPenjualan;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class GrafikPenjualanController extends Controller
{
    public function index()
    {
        // Debug: Cek apakah halaman terbuka
        \Log::info('GrafikPenjualanController@index called');

        return view('laporan.grafik-penjualan');
    }

    public function grafikProfit(Request $request)
    {
        // Debug log
        \Log::info('GrafikPenjualanController@grafikProfit called', $request->all());

        try {
            $range = $request->input('range', 'harian');

            // Cek apakah ada data TransaksiPenjualan
            $totalData = TransaksiPenjualan::count();
            \Log::info("Total TransaksiPenjualan: {$totalData}");

            if ($totalData == 0) {
                return response()->json([
                    ['label' => 'Tidak ada data', 'total_laba' => 0]
                ]);
            }

            $query = TransaksiPenjualan::query();

            // Cek apakah method selesai() ada
            if (method_exists(TransaksiPenjualan::class, 'selesai')) {
                $query = $query->selesai();
            } else {
                // Fallback jika scope selesai tidak ada
                $query = $query->where('status', 'selesai');
            }

            if ($range === 'harian') {
                $tanggal = $request->input('tanggal');
                if ($tanggal) {
                    $label = Carbon::parse($tanggal)->translatedFormat('d F Y');
                    $total = $query->whereDate('tanggal_transaksi', $tanggal)
                                   ->sum('laba_bruto');
                    $data = collect([['label' => $label, 'total_laba' => $total]]);
                } else {
                    // Default: hari ini
                    $today = now()->format('Y-m-d');
                    $total = $query->whereDate('tanggal_transaksi', $today)
                                   ->sum('laba_bruto');
                    $data = collect([['label' => 'Hari ini', 'total_laba' => $total]]);
                }
            }
            elseif ($range === 'bulanan') {
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
                $data = $weeks->map(fn($week) => [
                    'label' => $week,
                    'total_laba' => $group[$week]['total_laba'] ?? 0,
                ]);
            }
            elseif ($range === 'tahunan') {
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
                $data = $years->map(fn($year) => [
                    'label' => (string)$year,
                    'total_laba' => $group[$year]['total_laba'] ?? 0,
                ]);
            }
            elseif ($range === 'kustom') {
                $start = Carbon::parse($request->input('start_date'));
                $end = Carbon::parse($request->input('end_date'));
                $group = $query->whereBetween('tanggal_transaksi', [$start, $end])
                               ->get()
                               ->groupBy(fn($t) => Carbon::parse($t->tanggal_transaksi)->format('d M Y'))
                               ->map(fn($items, $label) => [
                                   'label' => $label,
                                   'total_laba' => $items->sum('laba_bruto'),
                               ]);

                $data = $group->values();
            }

            \Log::info('Profit data result', $data->toArray());
            return response()->json($data);

        } catch (\Exception $e) {
            \Log::error('Error in grafikProfit: ' . $e->getMessage());
            return response()->json([
                ['label' => 'Error', 'total_laba' => 0]
            ]);
        }
    }

    public function grafikProdukTerlaris(Request $request)
    {
        \Log::info('GrafikPenjualanController@grafikProdukTerlaris called', $request->all());

        try {
            $range = $request->input('range', 'harian');
            $end = now();

            $start = match($range) {
                'harian' => $request->input('tanggal')
                    ? Carbon::parse($request->input('tanggal'))->startOfDay()
                    : $end->copy()->startOfDay(),
                'bulanan' => $end->copy()->startOfMonth(),
                'tahunan' => $end->copy()->startOfYear(),
                'kustom' => Carbon::parse($request->input('start_date')),
                default => $end->copy()->startOfDay(),
            };

            $end = match($range) {
                'harian' => $request->input('tanggal')
                    ? Carbon::parse($request->input('tanggal'))->endOfDay()
                    : $end->copy()->endOfDay(),
                'kustom' => Carbon::parse($request->input('end_date')),
                default => $end,
            };

            $query = TransaksiPenjualan::with('barang');

            // Cek apakah method selesai() ada
            if (method_exists(TransaksiPenjualan::class, 'selesai')) {
                $query = $query->selesai();
            } else {
                $query = $query->where('status', 'selesai');
            }

            $data = $query->whereBetween('tanggal_transaksi', [$start, $end])
                ->get()
                ->groupBy('id_barang')
                ->map(fn($items, $barangId) => [
                    'id_barang' => $barangId,
                    'label' => optional($items->first()->barang)->nama_barang ?? 'Barang Tidak Diketahui',
                    'total_terjual' => $items->sum('jumlah_terjual'), // Sesuaikan dengan field di model
                ])
                ->sortByDesc('total_terjual')
                ->take(5)
                ->values();

            \Log::info('Produk data result', $data->toArray());
            return response()->json($data);

        } catch (\Exception $e) {
            \Log::error('Error in grafikProdukTerlaris: ' . $e->getMessage());
            return response()->json([]);
        }
    }

    public function getSummary(Request $request)
    {
        \Log::info('GrafikPenjualanController@getSummary called', $request->all());

        try {
            $range = $request->input('range', 'harian');
            $tanggal = $request->input('tanggal');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            // Tentukan periode berdasarkan range
            $start = match($range) {
                'harian' => $tanggal ? Carbon::parse($tanggal)->startOfDay() : now()->startOfDay(),
                'bulanan' => now()->startOfMonth(),
                'tahunan' => now()->startOfYear(),
                'kustom' => $startDate ? Carbon::parse($startDate) : now()->startOfMonth(),
                default => now()->startOfDay(),
            };

            $end = match($range) {
                'harian' => $tanggal ? Carbon::parse($tanggal)->endOfDay() : now()->endOfDay(),
                'bulanan' => now()->endOfMonth(),
                'tahunan' => now()->endOfYear(),
                'kustom' => $endDate ? Carbon::parse($endDate) : now(),
                default => now()->endOfDay(),
            };

            $query = TransaksiPenjualan::query();

            // Cek apakah method selesai() ada
            if (method_exists(TransaksiPenjualan::class, 'selesai')) {
                $query = $query->selesai();
            } else {
                $query = $query->where('status', 'selesai');
            }

            $query = $query->whereBetween('tanggal_transaksi', [$start, $end]);

            $totalProfit = $query->sum('laba_bruto') ?? 0;
            $totalPenjualan = $query->sum('total_harga') ?? 0;
            $totalTransaksi = $query->count();
            $totalProdukTerjual = $query->sum('jumlah_terjual') ?? 0;
            $avgProfit = $totalTransaksi > 0 ? $totalProfit / $totalTransaksi : 0;

            // Format periode untuk display
            $periodeText = match($range) {
                'harian' => $tanggal ? Carbon::parse($tanggal)->translatedFormat('d F Y') : 'Hari ini',
                'bulanan' => 'Bulan ' . now()->translatedFormat('F Y'),
                'tahunan' => 'Tahun ' . now()->year,
                'kustom' => ($startDate && $endDate)
                    ? Carbon::parse($startDate)->format('d/m/Y') . ' - ' . Carbon::parse($endDate)->format('d/m/Y')
                    : 'Periode kustom',
                default => 'Periode tidak diketahui'
            };

            $result = [
                'total_profit' => $totalProfit,
                'total_penjualan' => $totalPenjualan,
                'total_transaksi' => $totalTransaksi,
                'total_produk_terjual' => $totalProdukTerjual,
                'avg_profit' => $avgProfit,
                'periode' => $periodeText,
            ];

            \Log::info('Summary result', $result);
            return response()->json($result);

        } catch (\Exception $e) {
            \Log::error('Error in getSummary: ' . $e->getMessage());
            return response()->json([
                'total_profit' => 0,
                'total_penjualan' => 0,
                'total_transaksi' => 0,
                'total_produk_terjual' => 0,
                'avg_profit' => 0,
                'periode' => 'Error',
            ]);
        }
    }

    public function getTopProducts(Request $request)
    {
        // Method ini opsional, bisa di-comment jika tidak digunakan
        return response()->json([]);
    }

    public function export(Request $request)
    {
        // Method ini untuk nanti, bisa di-comment dulu
        return response()->json(['message' => 'Export feature coming soon']);
    }
}
