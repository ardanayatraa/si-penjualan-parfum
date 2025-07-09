<?php

namespace App\Livewire\Table;

use App\Models\TransaksiPembelian;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LaporanPembelianTable extends DataTableComponent
{
    protected $model = TransaksiPembelian::class;

    public string $startDate = '';
    public string $endDate   = '';

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('tanggal_transaksi', 'desc');
    }

    public function builder(): Builder
    {
        return TransaksiPembelian::query()
            ->with(['barang.supplier']);
    }

    public function filters(): array
    {
        return [
            DateFilter::make('Tanggal Mulai')
                ->config(['max' => now()->format('Y-m-d')])
                ->filter(function (Builder $builder, string $value) {
                    $this->startDate = $value;
                    return $builder->whereDate('tanggal_transaksi', '>=', $value);
                }),

            DateFilter::make('Tanggal Akhir')
                ->config(['max' => now()->format('Y-m-d')])
                ->filter(function (Builder $builder, string $value) {
                    $this->endDate = $value;
                    return $builder->whereDate('tanggal_transaksi', '<=', $value);
                }),
        ];
    }

    public function bulkActions(): array
    {
        return [
            'exportPdf' => 'Export PDF',
            'exportAllPdf' => 'Export All PDF',
        ];
    }

    public function exportPdf()
    {
        $selected = $this->getSelected();

        if (empty($selected)) {
            $this->addError('export', 'Pilih data yang akan diekspor terlebih dahulu.');
            return;
        }

        $query = TransaksiPembelian::with(['barang.supplier'])
            ->whereIn('id', $selected);

        // Apply date filters if exists
        if ($this->startDate) {
            $query->whereDate('tanggal_transaksi', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('tanggal_transaksi', '<=', $this->endDate);
        }

        $data = $query->orderBy('tanggal_transaksi', 'desc')->get();

        if ($data->isEmpty()) {
            $this->addError('export', 'Tidak ada data untuk diekspor.');
            return;
        }

        return $this->generatePdf($data, 'selected');
    }

    public function exportAllPdf()
    {
        $query = TransaksiPembelian::with(['barang.supplier']);

        // Apply date filters if exists
        if ($this->startDate) {
            $query->whereDate('tanggal_transaksi', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('tanggal_transaksi', '<=', $this->endDate);
        }

        $data = $query->orderBy('tanggal_transaksi', 'desc')->get();

        if ($data->isEmpty()) {
            $this->addError('export', 'Tidak ada data untuk diekspor.');
            return;
        }

        return $this->generatePdf($data, 'all');
    }

    private function generatePdf($data, $type = 'selected')
    {
        // Determine date range
        $start = $this->startDate
            ?: ($data->min('tanggal_transaksi')?->format('Y-m-d') ?? now()->format('Y-m-d'));
        $end = $this->endDate
            ?: ($data->max('tanggal_transaksi')?->format('Y-m-d') ?? now()->format('Y-m-d'));

        // Calculate totals
        $totalPembelian = $data->sum('jumlah_pembelian');
        $grandTotal = $data->sum('total');

        $pdf = Pdf::loadView('exports.pembelian-pdf', [
            'data'           => $data,
            'start_date'     => Carbon::parse($start)->format('d M Y'),
            'end_date'       => Carbon::parse($end)->format('d M Y'),
            'total_pembelian' => $totalPembelian,
            'grand_total'    => $grandTotal,
            'export_type'    => $type,
        ])->setPaper('a4', 'landscape');

        $this->clearSelected();

        $filename = "laporan-pembelian_{$type}_{$start}_{$end}.pdf";

        return response()->streamDownload(
            fn() => print($pdf->stream()),
            $filename
        );
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable(),

            Column::make('Barang', 'barang.nama_barang')
                ->sortable()
                ->searchable(),

            Column::make('Supplier', 'barang.supplier.nama_supplier')
                ->sortable()
                ->searchable(),

            Column::make('Tanggal Transaksi', 'tanggal_transaksi')
                ->sortable()
                ->format(fn($val) => Carbon::parse($val)->format('d-m-Y')),

            Column::make('Jumlah Pembelian', 'jumlah_pembelian')
                ->sortable(),

            Column::make('Total (Rp)', 'total')
                ->sortable()
                ->format(fn($val) => 'Rp ' . number_format($val, 0, ',', '.')),
        ];
    }
}
