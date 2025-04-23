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
                    return $builder->whereDate('tanggal', '>=', $value);
                }),

            DateFilter::make('Tanggal Akhir')
                ->config(['max' => now()->format('Y-m-d')])
                ->filter(function (Builder $builder, string $value) {
                    $this->endDate = $value;
                    return $builder->whereDate('tanggal', '<=', $value);
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

        $query = TransaksiPembelian::with(['barang', 'supplier'])
            ->when(!empty($selectedItems), fn($q) => $q->whereIn('id', $selectedItems));

        $start_date = $this->startDate;
        $end_date = $this->endDate;

        if ($start_date && $end_date) {
            $this->validate([
                'startDate' => 'required|date',
                'endDate' => 'required|date|after_or_equal:startDate',
            ]);
            $query->whereBetween('tanggal', [$start_date, $end_date]);
        } else {
            $dataTemp = $query->clone()->get();
            $minTanggal = $dataTemp->min('tanggal');
            $maxTanggal = $dataTemp->max('tanggal');

            $start_date = $minTanggal
                ? Carbon::parse($minTanggal)->format('Y-m-d')
                : now()->format('Y-m-d');

            $end_date = $maxTanggal
                ? Carbon::parse($maxTanggal)->format('Y-m-d')
                : now()->format('Y-m-d');
        }

        $data = $query->get();

        $pdf = Pdf::loadView('exports.pembelian-pdf', [
            'data' => $data,
            'start_date' => Carbon::parse($start_date)->translatedFormat('d M Y'),
            'end_date' => Carbon::parse($end_date)->translatedFormat('d M Y'),
        ])->setPaper('a4', 'landscape');

        $this->clearSelected();

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'laporan-pembelian.pdf');
    }

    public function builder(): Builder
    {
        return TransaksiPembelian::query()->with(['barang', 'supplier']);
    }

    public function columns(): array
    {
        return [
            Column::make("ID", "id")->sortable(),
            Column::make("Barang", "barang.nama_barang")->sortable(),
            Column::make("Supplier", "supplier.nama_supplier")->sortable(),
            Column::make("Tanggal", "tanggal")
                ->sortable()
                ->format(fn($val) => Carbon::parse($val)->format('d-m-Y')),
            Column::make("Jumlah", "jumlah")->sortable(),
            Column::make("Harga Beli", "harga_beli")
                ->format(fn($val) => 'Rp ' . number_format($val, 0, ',', '.')),
            Column::make("Total Harga Beli", "total_harga_beli")
                ->format(fn($val) => 'Rp ' . number_format($val, 0, ',', '.')),
            Column::make("Total Nilai Transaksi", "total_nilai_transaksi")
                ->format(fn($val) => 'Rp ' . number_format($val, 0, ',', '.')),
            Column::make("Keterangan", "keterangan"),
        ];
    }
}
