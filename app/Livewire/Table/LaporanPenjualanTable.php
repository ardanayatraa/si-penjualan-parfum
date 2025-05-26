<?php

namespace App\Livewire\Table;

use App\Models\TransaksiPenjualan;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LaporanPenjualanTable extends DataTableComponent
{
    protected $model = TransaksiPenjualan::class;

    public string $startDate = '';
    public string $endDate   = '';

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function builder(): Builder
    {
        return TransaksiPenjualan::query()
            ->with(['kasir', 'barang', 'pajak']);
    }

    public function filters(): array
    {
        return [
            DateFilter::make('Tanggal Mulai')
                ->config(['max' => now()->format('Y-m-d')])
                ->filter(fn(Builder $builder, string $value) =>
                    $builder->whereDate('tanggal_transaksi', '>=', $this->startDate = $value)
                ),

            DateFilter::make('Tanggal Akhir')
                ->config(['max' => now()->format('Y-m-d')])
                ->filter(fn(Builder $builder, string $value) =>
                    $builder->whereDate('tanggal_transaksi', '<=', $this->endDate = $value)
                ),
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
        $query = TransaksiPenjualan::with(['kasir', 'barang', 'pajak'])
            ->when($selected, fn($q) => $q->whereIn('id', $selected));

        if ($this->startDate && $this->endDate) {
            $this->validate([
                'startDate' => 'required|date',
                'endDate'   => 'required|date|after_or_equal:startDate',
            ]);
            $query->whereBetween('tanggal_transaksi', [$this->startDate, $this->endDate]);
        }

        $data = $query->get();
        $start = $this->startDate ?: ($data->min('tanggal_transaksi')?->format('Y-m-d') ?? now()->format('Y-m-d'));
        $end   = $this->endDate   ?: ($data->max('tanggal_transaksi')?->format('Y-m-d') ?? now()->format('Y-m-d'));

        $pdf = Pdf::loadView('exports.penjualan-pdf', [
            'data'       => $data,
            'start_date' => $start,
            'end_date'   => $end,
        ])->setPaper('a4', 'landscape');

        $this->clearSelected();

        return response()->streamDownload(fn() => print($pdf->stream()), "laporan-penjualan_{$start}_{$end}.pdf");
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')->sortable(),

            Column::make('Kasir', 'kasir.name')
                ->sortable()
                ->format(fn($value,$row) => $row->kasir->name ?? '-'),

            Column::make('Barang', 'barang.nama_barang')
                ->sortable()
                ->format(fn($value,$row) => $row->barang->nama_barang),

            Column::make('Pajak', 'pajak.nama')
                ->sortable()
                ->format(fn($value,$row) => "{$row->pajak->nama} ({$row->pajak->presentase}%)"),

            Column::make('Tanggal', 'tanggal_transaksi')
                ->sortable()
                ->format(fn($value) => Carbon::parse($value)->format('d-m-Y')),

            Column::make('Subtotal', 'subtotal')
                ->sortable()
                ->format(fn($value) => 'Rp ' . number_format($value, 0, ',', '.')),

            Column::make('Harga Pokok', 'harga_pokok')
                ->sortable()
                ->format(fn($value) => 'Rp ' . number_format($value, 0, ',', '.')),

            Column::make('Laba Bruto', 'laba_bruto')
                ->sortable()
                ->format(fn($value) => 'Rp ' . number_format($value, 0, ',', '.')),

            Column::make('Total Harga', 'total_harga')
                ->sortable()
                ->format(fn($value) => 'Rp ' . number_format($value, 0, ',', '.')),
        ];
    }
}
