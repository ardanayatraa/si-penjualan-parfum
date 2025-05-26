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
                $label = Carbon::parse($tanggal)->translatedFormat('l');
                $total = $query->whereDate('tanggal_transaksi', $tanggal)
                               ->sum('laba_bruto');
                $data = collect([['label' => $label, 'total_laba' => $total]]);
            } else {
                $start = now()->startOfWeek();
                $end   = now()->endOfWeek();
                $group = $query->whereBetween('tanggal_transaksi', [$start, $end])
                               ->get()
                               ->groupBy(fn($t) => Carbon::parse($t->tanggal_transaksi)->translatedFormat('l'))
                               ->map(fn($items, $day) => [
                                   'label'     => $day,
                                   'total_laba'=> $items->sum('laba_bruto'),
                               ]);
                $allDays = collect(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday']);
                $data = $allDays->map(fn($d) => [
                    'label'      => $d,
                    'total_laba' => $group[$d]['total_laba'] ?? 0,
                ]);
            }
        }
        elseif ($range === 'bulanan') {
            $start = now()->startOfMonth();
            $end   = now()->endOfMonth();
            $group = $query->whereBetween('tanggal_transaksi', [$start, $end])
                           ->get()
                           ->groupBy(fn($t) => 'Week '.ceil(Carbon::parse($t->tanggal_transaksi)->day/7))
                           ->map(fn($items,$wk) => [
                               'label'      => $wk,
                               'total_laba' => $items->sum('laba_bruto'),
                           ]);
            $weeks = collect(range(1,5))->map(fn($i)=>"Week {$i}");
            $data  = $weeks->map(fn($wk)=>[
                'label'      => $wk,
                'total_laba' => $group[$wk]['total_laba'] ?? 0,
            ]);
        }
        elseif ($range === 'tahunan') {
            $start = now()->subYears(4)->startOfYear();
            $end   = now()->endOfYear();
            $group = $query->whereBetween('tanggal_transaksi', [$start, $end])
                           ->get()
                           ->groupBy(fn($t)=>Carbon::parse($t->tanggal_transaksi)->year)
                           ->map(fn($items,$yr)=>[
                               'label'      => (string)$yr,
                               'total_laba' => $items->sum('laba_bruto'),
                           ]);
            $years = collect(range(now()->year-4, now()->year));
            $data  = $years->map(fn($yr)=>[
                'label'      => (string)$yr,
                'total_laba' => $group[$yr]['total_laba'] ?? 0,
            ]);
        }
        elseif ($range === 'kustom') {
            $start = Carbon::parse($request->input('start_date'));
            $end   = Carbon::parse($request->input('end_date'));
            $group = $query->whereBetween('tanggal_transaksi', [$start, $end])
                           ->get()
                           ->groupBy(fn($t)=>Carbon::parse($t->tanggal_transaksi)->format('d M Y'))
                           ->map(fn($items,$lbl)=>[
                               'label'      => $lbl,
                               'total_laba' => $items->sum('laba_bruto'),
                           ]);
            $data = $group->values();
        }

        return response()->json($data);
    }

    public function grafikProdukTerlaris(Request $request)
    {
        $range = $request->input('range', 'harian');
        $end   = now();
        $start = match($range) {
            'harian'  => $request->input('tanggal')
                          ? Carbon::parse($request->input('tanggal'))->startOfDay()
                          : $end->copy()->startOfWeek(),
            'bulanan' => $end->copy()->startOfMonth(),
            'tahunan' => $end->copy()->startOfYear(),
            'kustom'  => Carbon::parse($request->input('start_date')),
            default   => $end->copy()->startOfWeek(),
        };
        $end = match($range) {
            'harian'  => $request->input('tanggal')
                          ? Carbon::parse($request->input('tanggal'))->endOfDay()
                          : $end->copy()->endOfWeek(),
            'kustom'  => Carbon::parse($request->input('end_date')),
            default   => $end,
        };

        $data = TransaksiPenjualan::with('barang')
            ->whereBetween('tanggal_transaksi', [$start, $end])
            ->get()
            ->groupBy('id_barang')
            ->map(fn($items, $bid) => [
                'id_barang'    => $bid,
                'label'        => optional($items->first()->barang)->nama_barang ?? '-',
                'total_terjual'=> $items->sum('jumlah_penjualan'),
            ])
            ->sortByDesc('total_terjual')
            ->take(5)
            ->values();

        return response()->json($data);
    }
}
