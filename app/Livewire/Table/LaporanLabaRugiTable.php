<?php

namespace App\Livewire\Table;

use App\Models\TransaksiPenjualan;
use App\Models\TransaksiPembelian;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LaporanLabaRugiTable extends DataTableComponent
{
    // Kita tidak punya model tunggal—gunakan builder manual via DB
    public string $startDate = '';
    public string $endDate   = '';

    public function configure(): void
    {
        $this->setPrimaryKey('tgl');
    }

    public function builder(): Builder
    {
        $subquery = TransaksiPenjualan::selectRaw("
                DATE(tanggal_transaksi) as tgl,
                SUM(total_harga) as total_penjualan
            ")
            ->groupBy('tgl');

        // Kita join dengan total pembelian per tanggal
        return DB::table('transaksi_pembelian')
            ->selectRaw("
                COALESCE(penjualan.tgl, pembelian.tgl) as tgl,
                COALESCE(penjualan.total_penjualan, 0) as total_penjualan,
                COALESCE(pembelian.total_pembelian, 0) as total_pembelian,
                (COALESCE(penjualan.total_penjualan, 0) - COALESCE(pembelian.total_pembelian, 0)) as laba_rugi
            ")
            ->leftJoinSub($subquery, 'penjualan', function($join) {
                $join->on('penjualan.tgl', '=', DB::raw('DATE(transaksi_pembelian.tanggal_pembelian)'));
            })
            ->selectRaw("
                COALESCE(penjualan.tgl, DATE(transaksi_pembelian.tanggal_pembelian)) as tgl
            ")
            ->groupByRaw('tgl')
            ->orderByRaw('tgl');
    }

    public function filters(): array
    {
        return [
            DateFilter::make('Tanggal Mulai')
                ->config(['max' => now()->format('Y-m-d')])
                ->filter(fn($q, $v) => $q->whereDate('tgl', '>=', $this->startDate = $v)),
            DateFilter::make('Tanggal Akhir')
                ->config(['max' => now()->format('Y-m-d')])
                ->filter(fn($q, $v) => $q->whereDate('tgl', '<=', $this->endDate = $v)),
        ];
    }

    public function bulkActions(): array
    {
        return ['exportPdf' => 'Export PDF'];
    }

    public function exportPdf()
    {
        $selected = $this->getSelected();

        // Ambil data dengan logika yang sama seperti builder(), tapi di‐fetch ke collection
        $subquery = TransaksiPenjualan::selectRaw("
                DATE(tanggal_transaksi) as tgl,
                SUM(total_harga) as total_penjualan
            ")
            ->groupBy('tgl');

        $query = DB::table('transaksi_pembelian')
            ->selectRaw("
                COALESCE(penjualan.tgl, pembelian.tgl) as tgl,
                COALESCE(penjualan.total_penjualan, 0) as total_penjualan,
                COALESCE(pembelian.total_pembelian, 0) as total_pembelian,
                (COALESCE(penjualan.total_penjualan, 0) - COALESCE(pembelian.total_pembelian, 0)) as laba_rugi
            ")
            ->leftJoinSub($subquery, 'penjualan', function($join) {
                $join->on('penjualan.tgl', '=', DB::raw('DATE(transaksi_pembelian.tanggal_pembelian)'));
            })
            ->selectRaw("
                COALESCE(penjualan.tgl, DATE(transaksi_pembelian.tanggal_pembelian)) as tgl
            ")
            ->groupByRaw('tgl')
            ->orderByRaw('tgl');

        if ($this->startDate && $this->endDate) {
            $this->validate([
                'startDate' => 'required|date',
                'endDate'   => 'required|date|after_or_equal:startDate',
            ]);
            $query->whereBetween(DB::raw('tgl'), [$this->startDate, $this->endDate]);
        }

        if ($selected) {
            // Jika user memilih beberapa baris (berdasarkan tgl), filter manual
            $query->whereIn('tgl', $selected);
        }

        $data = $query->get();
        $start = $this->startDate ?: ($data->min('tgl') ?? now()->format('Y-m-d'));
        $end   = $this->endDate   ?: ($data->max('tgl') ?? now()->format('Y-m-d'));

        $pdf = Pdf::loadView('exports.labarugi-pdf', [
            'data'  => $data,
            'start' => Carbon::parse($start)->format('d-m-Y'),
            'end'   => Carbon::parse($end)->format('d-m-Y'),
        ])->setPaper('a4','landscape');

        $this->clearSelected();

        return response()->streamDownload(
            fn() => print($pdf->stream()),
            "laporan-labarugi_{$start}_{$end}.pdf"
        );
    }

    public function columns(): array
    {
        return [
            Column::make('Tanggal', 'tgl')
                ->sortable()
                ->format(fn($v) => Carbon::parse($v)->format('d-m-Y')),

            Column::make('Total Penjualan (Rp)', 'total_penjualan')
                ->sortable()
                ->format(fn($v) => 'Rp '.number_format($v, 0, ',', '.')),

            Column::make('Total Pembelian (Rp)', 'total_pembelian')
                ->sortable()
                ->format(fn($v) => 'Rp '.number_format($v, 0, ',', '.')),

            Column::make('Laba/Rugi (Rp)', 'laba_rugi')
                ->sortable()
                ->format(fn($v) => 'Rp '.number_format($v, 0, ',', '.')),
        ];
    }
}
