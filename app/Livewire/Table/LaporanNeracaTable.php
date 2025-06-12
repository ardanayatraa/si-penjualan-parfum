<?php

namespace App\Livewire\Table;

use App\Models\Akun;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LaporanNeracaTable extends DataTableComponent
{
    public string $startDate = '';
    public string $endDate   = '';

    protected $model = Akun::class;

    public function configure(): void
    {
        $this->setPrimaryKey('kode_akun');
        $this->setDefaultSort('kode_akun', 'asc');
    }

    public function builder(): Builder
    {
        return Akun::query();
    }

    public function filters(): array
    {
        return [
            DateFilter::make('Dari Tanggal')
                ->config(['max' => now()->format('Y-m-d')])
                ->filter(function(Builder $builder, string $value) {
                    $this->startDate = $value;
                    return $builder->whereHas('detailJurnal.jurnalUmum', fn($q) =>
                        $q->whereDate('tanggal', '>=', $value)
                    );
                }),

            DateFilter::make('Sampai Tanggal')
                ->config(['max' => now()->format('Y-m-d')])
                ->filter(function(Builder $builder, string $value) {
                    $this->endDate = $value;
                    return $builder->whereHas('detailJurnal.jurnalUmum', fn($q) =>
                        $q->whereDate('tanggal', '<=', $value)
                    );
                }),
        ];
    }

    public function columns(): array
    {
        return [
            Column::make('No Akun', 'kode_akun')->sortable(),
            Column::make('Nama Akun', 'nama_akun')->sortable(),
        ];
    }

    private function sumSide($akun, string $field): float
    {
        $query = $akun->detailJurnal()->with('jurnalUmum');
        if ($this->startDate) {
            $query->whereHas('jurnalUmum', fn($q) =>
                $q->whereDate('tanggal', '>=', $this->startDate)
            );
        }
        if ($this->endDate) {
            $query->whereHas('jurnalUmum', fn($q) =>
                $q->whereDate('tanggal', '<=', $this->endDate)
            );
        }
        return (float) $query->sum($field);
    }

    private function formatRupiah(float $angka): string
    {
        return $angka > 0
            ? 'Rp '.number_format($angka, 0, ',', '.')
            : '-';
    }

    public function bulkActions(): array
    {
        return ['exportPdf' => 'Export PDF'];
    }

    public function exportPdf()
    {
        $selected = $this->getSelected();
        $akunQuery = Akun::query()
            ->when($selected, fn($q) => $q->whereIn('kode_akun', $selected));

        $list = $akunQuery->orderBy('kode_akun')->get()->map(function($row) {
            return [
                'kode'   => $row->kode_akun,
                'nama'   => $row->nama_akun,
                'debet'  => $this->sumSide($row, 'debit'),
                'kredit' => $this->sumSide($row, 'kredit'),
            ];
        });

        $totalDebet  = $list->sum('debet');
        $totalKredit = $list->sum('kredit');

        $start = $this->startDate ?: '';
        $end   = $this->endDate   ?: '';

        $pdf = Pdf::loadView('exports.neraca-pdf', compact(
            'list','totalDebet','totalKredit','start','end'
        ))->setPaper('a4', 'landscape');

        $this->clearSelected();

        return response()->streamDownload(
            fn() => print($pdf->stream()),
            "laporan-neraca_{$start}_{$end}.pdf"
        );
    }
}
