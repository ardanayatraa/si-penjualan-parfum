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
        ];
    }

    public function exportPdf()
    {
        $selected = $this->getSelected();
        $query = TransaksiPembelian::with(['barang.supplier'])
            ->when($selected, fn($q) => $q->whereIn('id', $selected));

        if ($this->startDate && $this->endDate) {
            $this->validate([
                'startDate' => 'required|date',
                'endDate'   => 'required|date|after_or_equal:startDate',
            ]);
            $query->whereBetween('tanggal_transaksi', [$this->startDate, $this->endDate]);
        }

        $data = $query->get();

        $start = $this->startDate
            ?: ($data->min('tanggal_transaksi')?->format('Y-m-d') ?? now()->format('Y-m-d'));
        $end = $this->endDate
            ?: ($data->max('tanggal_transaksi')?->format('Y-m-d') ?? now()->format('Y-m-d'));

        $pdf = Pdf::loadView('exports.pembelian-pdf', [
            'data'       => $data,
            'start_date' => Carbon::parse($start)->format('d M Y'),
            'end_date'   => Carbon::parse($end)->format('d M Y'),
        ])->setPaper('a4', 'landscape');

        $this->clearSelected();

        return response()->streamDownload(fn() => print($pdf->stream()),
            "laporan-pembelian_{$start}_{$end}.pdf"
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
