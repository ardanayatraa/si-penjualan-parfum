<?php

namespace App\Livewire\Table;

use App\Models\Akun;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Rappasoft\LaravelLivewireTables\Views\Columns\SumColumn;

class LaporanLabaRugiTable extends DataTableComponent
{
    public string $startDate = '';
    public string $endDate   = '';

    // Hanya ambil akun Pendapatan & Beban
    protected $model = Akun::class;

    public function configure(): void
    {
        $this->setPrimaryKey('kode_akun');
        $this->setDefaultSort('kode_akun', 'asc');
    }

    public function builder(): Builder
    {
        return Akun::query()
            ->whereIn('tipe_akun', ['Pendapatan','Beban']);
    }

    public function filters(): array
    {
        return [
            DateFilter::make('Dari Tanggal')
                ->config(['max' => now()->format('Y-m-d')])
                ->filter(fn(Builder $b, string $v) => $this->startDate = $v),

            DateFilter::make('Sampai Tanggal')
                ->config(['max' => now()->format('Y-m-d')])
                ->filter(fn(Builder $b, string $v) => $this->endDate = $v),
        ];
    }

    public function columns(): array
    {
        return [
            Column::make('No Akun', 'kode_akun')->sortable(),
            Column::make('Nama Akun', 'nama_akun')->sortable(),
            SumColumn::make('Debit')
            ->setDataSource('detailJurnal','debit')
            ->sortable(),
            SumColumn::make('Credit')
            ->setDataSource('detailJurnal','kredit')
            ->sortable(),
        ];
    }

    private function sumAccount(Akun $akun): float
    {
        $q = $akun->detailJurnal()->with('jurnalUmum');
        if ($this->startDate) {
            $q->whereHas('jurnalUmum', fn($j) =>
                $j->whereDate('tanggal', '>=', $this->startDate)
            );
        }
        if ($this->endDate) {
            $q->whereHas('jurnalUmum', fn($j) =>
                $j->whereDate('tanggal', '<=', $this->endDate)
            );
        }

        // Pendapatan: sum kredit; Beban: sum debit
        return $akun->tipe_akun === 'Pendapatan'
            ? (float)$q->sum('kredit')
            : (float)$q->sum('debit');
    }

    private function formatRupiah(float $v): string
    {
        return $v > 0
            ? 'Rp '.number_format($v,0,',','.')
            : '-';
    }

    public function bulkActions(): array
    {
        return ['exportPdf' => 'Export PDF'];
    }

    public function exportPdf()
    {
        $selected = $this->getSelected();
        $query = Akun::whereIn('tipe_akun',['Pendapatan','Beban'])
            ->when($selected, fn($q) => $q->whereIn('kode_akun',$selected))
            ->orderBy('kode_akun');

        $rows = $query->get()->map(fn($r) => [
            'kode'  => $r->kode_akun,
            'nama'  => $r->nama_akun,
            'tipe'  => $r->tipe_akun,
            'jumlah'=> $this->sumAccount($r),
        ]);

        $totalPdpt = $rows->filter(fn($r) => $r['tipe']==='Pendapatan')->sum('jumlah');
        $totalBeban= $rows->filter(fn($r) => $r['tipe']==='Beban')->sum('jumlah');
        $labaRugi  = $totalPdpt - $totalBeban;

        $start = $this->startDate ?: '';
        $end   = $this->endDate   ?: '';

        $pdf = Pdf::loadView('exports.laba-rugi-pdf', compact(
            'rows','totalPdpt','totalBeban','labaRugi','start','end'
        ))->setPaper('a4','landscape');

        $this->clearSelected();

        return response()->streamDownload(
            fn() => print($pdf->stream()),
            "laporan-laba-rugi_{$start}_{$end}.pdf"
        );
    }
}
