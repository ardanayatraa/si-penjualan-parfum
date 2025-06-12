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
                ->filter(fn(Builder $q, string $v) => $q->whereDate('tanggal_transaksi', '>=', $this->startDate = $v)),
            DateFilter::make('Tanggal Akhir')
                ->config(['max' => now()->format('Y-m-d')])
                ->filter(fn(Builder $q, string $v) => $q->whereDate('tanggal_transaksi', '<=', $this->endDate = $v)),
        ];
    }

    public function bulkActions(): array
    {
        return ['exportPdf' => 'Export PDF'];
    }

    public function exportPdf()
    {
        $selected = $this->getSelected();
        $q = TransaksiPenjualan::with(['kasir','barang','pajak'])
            ->when($selected, fn($q) => $q->whereIn('id', $selected));

        if ($this->startDate && $this->endDate) {
            $this->validate([
                'startDate' => 'required|date',
                'endDate'   => 'required|date|after_or_equal:startDate',
            ]);
            $q->whereBetween('tanggal_transaksi', [$this->startDate, $this->endDate]);
        }

        $data  = $q->get();
        $start = $this->startDate ?: ($data->min('tanggal_transaksi')?->format('Y-m-d') ?? now()->format('Y-m-d'));
        $end   = $this->endDate   ?: ($data->max('tanggal_transaksi')?->format('Y-m-d') ?? now()->format('Y-m-d'));

        $pdf = Pdf::loadView('exports.penjualan-pdf', compact('data','start','end'))
                  ->setPaper('a4','landscape');

        $this->clearSelected();

        return response()->streamDownload(fn() => print($pdf->stream()), "laporan-penjualan_{$start}_{$end}.pdf");
    }

    public function columns(): array
    {
        return [
            Column::make('ID','id')->sortable(),

            Column::make('Kasir','kasir.username')
                ->sortable()
          ,

            Column::make('Barang','barang.nama_barang')
                ->sortable()
          ,

            Column::make('Tanggal','tanggal_transaksi')
                ->sortable()
                ->format(fn($v) => Carbon::parse($v)->format('d-m-Y')),

            Column::make('Jumlah','jumlah_penjualan')
                ->sortable(),

            Column::make('Subtotal','subtotal')
                ->sortable()
                ->format(fn($v) => 'Rp '.number_format($v,0,',','.')),

            Column::make('Harga Pokok','harga_pokok')
                ->sortable()
                ->format(fn($v) => 'Rp '.number_format($v,0,',','.')),

            Column::make('Laba Bruto','laba_bruto')
                ->sortable()
                ->format(fn($v) => 'Rp '.number_format($v,0,',','.')),

            Column::make('Total Harga','total_harga')
                ->sortable()
                ->format(fn($v) => 'Rp '.number_format($v,0,',','.')),
        ];
    }
}
