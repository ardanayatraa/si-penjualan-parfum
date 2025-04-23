<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransaksiPenjualan;
use Carbon\Carbon;

class GrafikPenjualanController extends Controller
{
    public function index()
    {
        return view('laporan.grafik-penjualan');
    }

    public function grafikProfit(Request $request)
    {
        $range = $request->input('range', 'harian');
        $query = TransaksiPenjualan::query();

        if ($range === 'harian') {
            $tanggal = $request->input('tanggal');

            if ($tanggal) {
                $label = Carbon::parse($tanggal)->format('l'); // e.g. Monday
                $total = $query->whereDate('tanggal_transaksi', $tanggal)->sum('laba_bersih');

                $data = collect([
                    ['label' => $label, 'total_laba' => $total]
                ]);
            } else {
                $start = now()->startOfWeek(Carbon::MONDAY);
                $end = now()->endOfWeek(Carbon::SUNDAY);

                $result = $query->whereBetween('tanggal_transaksi', [$start, $end])
                    ->get()
                    ->groupBy(fn($item) => Carbon::parse($item->tanggal_transaksi)->format('l')) // Monday–Sunday
                    ->map(fn($items, $label) => [
                        'label' => $label,
                        'total_laba' => $items->sum('laba_bersih'),
                    ]);

                $allDays = collect(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']);

                $data = $allDays->map(fn($day) => [
                    'label' => $day,
                    'total_laba' => $result[$day]['total_laba'] ?? 0,
                ]);
            }
        }

        elseif ($range === 'bulanan') {
            $start = now()->startOfMonth();
            $end = now()->endOfMonth();

            $result = $query->whereBetween('tanggal_transaksi', [$start, $end])
                ->get()
                ->groupBy(fn($item) => 'Week ' . ceil(Carbon::parse($item->tanggal_transaksi)->day / 7))
                ->map(fn($items, $label) => [
                    'label' => $label,
                    'total_laba' => $items->sum('laba_bersih'),
                ]);

            $weeks = collect(range(1, 5))->map(fn($i) => 'Week ' . $i);

            $data = $weeks->map(fn($week) => [
                'label' => $week,
                'total_laba' => $result[$week]['total_laba'] ?? 0,
            ]);
        }

        elseif ($range === 'tahunan') {
            $start = now()->subYears(4)->startOfYear();
            $end = now()->endOfYear();

            $result = $query->whereBetween('tanggal_transaksi', [$start, $end])
                ->get()
                ->groupBy(fn($item) => Carbon::parse($item->tanggal_transaksi)->year)
                ->map(fn($items, $label) => [
                    'label' => (string) $label,
                    'total_laba' => $items->sum('laba_bersih'),
                ]);

            $years = collect(range(now()->year - 4, now()->year));

            $data = $years->map(fn($year) => [
                'label' => (string) $year,
                'total_laba' => $result[$year]['total_laba'] ?? 0,
            ]);
        }

        elseif ($range === 'kustom') {
            $start = Carbon::parse($request->input('start_date'));
            $end = Carbon::parse($request->input('end_date'));

            $result = $query->whereBetween('tanggal_transaksi', [$start, $end])
                ->get()
                ->groupBy(fn($item) => Carbon::parse($item->tanggal_transaksi)->format('d M Y'))
                ->map(fn($items, $label) => [
                    'label' => $label,
                    'total_laba' => $items->sum('laba_bersih'),
                ]);

            $data = $result->values();
        }

        return response()->json($data);
    }

    public function grafikProdukTerlaris(Request $request)
    {
        $range = $request->input('range', 'harian');
        $end = now();

        $start = match ($range) {
            'harian' => $request->input('tanggal')
                ? Carbon::parse($request->input('tanggal'))->startOfDay()
                : $end->copy()->startOfWeek(),
            'bulanan' => $end->copy()->startOfMonth(),
            'tahunan' => $end->copy()->startOfYear(),
            'kustom' => Carbon::parse($request->input('start_date')),
            default => $end->copy()->startOfWeek(),
        };

        $end = match ($range) {
            'harian' => $request->input('tanggal')
                ? Carbon::parse($request->input('tanggal'))->endOfDay()
                : $end->copy()->endOfDay(),
            'kustom' => Carbon::parse($request->input('end_date')),
            default => $end,
        };

        $data = TransaksiPenjualan::with('barang')
            ->whereBetween('tanggal_transaksi', [$start, $end])
            ->get()
            ->groupBy('id_barang')
            ->map(function ($items, $id_barang) {
                return [
                    'id_barang' => $id_barang,
                    'label' => optional($items->first()->barang)->nama_barang ?? 'Tidak diketahui',
                    'total_terjual' => $items->sum('jumlah'),
                ];
            })
            ->sortByDesc('total_terjual')
            ->take(5)
            ->values();

        return response()->json($data);
    }
}
