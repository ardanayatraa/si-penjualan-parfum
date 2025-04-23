<?php

namespace App\Livewire\Table;

use App\Models\TransaksiPenjualan;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanPenjualanTable extends DataTableComponent
{
    protected $model = TransaksiPenjualan::class;

    public string $startDate = '';
    public string $endDate = '';

    public function configure(): void
    {
        $this->setPrimaryKey('id');
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
        $selectedItems = $this->getSelected();

        $query = TransaksiPenjualan::with(['kasir', 'barang', 'pajak'])
            ->when(!empty($selectedItems), fn($q) => $q->whereIn('id', $selectedItems));

        $start_date = $this->startDate;
        $end_date = $this->endDate;

        if ($start_date && $end_date) {
            $this->validate([
                'startDate' => 'required|date',
                'endDate' => 'required|date|after_or_equal:startDate',
            ]);
            $query->whereBetween('tanggal_transaksi', [$start_date, $end_date]);
        } else {
            $dataTemp = $query->clone()->get();
            $start_date = $dataTemp->min('tanggal') ? \Carbon\Carbon::parse($dataTemp->min('tanggal_transaksi'))->format('Y-m-d') : now()->format('Y-m-d');
            $end_date = $dataTemp->max('tanggal') ? \Carbon\Carbon::parse($dataTemp->max('tanggal_transaksi'))->format('Y-m-d') : now()->format('Y-m-d');
        }

        $data = $query->get();

        $pdf = Pdf::loadView('exports.penjualan-pdf', [
            'data' => $data,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ])->setPaper('a4', 'landscape');

        $this->clearSelected();

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'laporan-penjualan.pdf');
    }

    public function builder(): Builder
    {
        return TransaksiPenjualan::query()->with(['kasir', 'barang', 'pajak']);
    }

    public function columns(): array
    {
        return [
            Column::make("ID", "id")->sortable(),
            Column::make("Kasir", "kasir.username")->sortable(),
            Column::make("Barang", "barang.nama_barang")->sortable(),
            Column::make("Tanggal Transaksi", "tanggal_transaksi")
                ->sortable()
                ->format(fn($val) => \Carbon\Carbon::parse($val)->format('d-m-Y')),
            Column::make("Jumlah", "jumlah")->sortable(),
            Column::make("Harga Jual", "harga_jual")
                ->format(fn($val) => 'Rp ' . number_format($val, 0, ',', '.')),
            Column::make("Pajak", "pajak.nilai_pajak")
                ->format(fn($val) => 'Rp ' . number_format($val, 0, ',', '.')),
            Column::make("Total Harga", "total_harga")
                ->format(fn($val) => 'Rp ' . number_format($val, 0, ',', '.')),
            Column::make("Total Nilai Transaksi", "total_nilai_transaksi")
                ->format(fn($val) => 'Rp ' . number_format($val, 0, ',', '.')),
            Column::make("Laba Bruto", "laba_bruto")
                ->format(fn($val) => 'Rp ' . number_format($val, 0, ',', '.')),
            Column::make("Laba Bersih", "laba_bersih")
                ->format(fn($val) => 'Rp ' . number_format($val, 0, ',', '.')),
            Column::make("Keterangan", "keterangan"),
        ];
    }
}
