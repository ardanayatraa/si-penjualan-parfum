<?php

namespace App\Livewire\Table;

use App\Models\Akun;
use App\Models\JurnalUmum;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LaporanNeracaTable extends DataTableComponent
{
    public string $startDate = '';
    public string $endDate   = '';

    protected $model = Akun::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id_akun');
        $this->setDefaultSort('kode_akun', 'asc');
    }

    public function builder(): Builder
    {
        return Akun::query()
            ->whereIn('tipe_akun', ['Aset', 'Kewajiban', 'Ekuitas']) // Hanya akun neraca
            ->orderBy('kode_akun');
    }

    public function filters(): array
    {
        return [
            DateFilter::make('Dari Tanggal')
                ->config(['max' => now()->format('Y-m-d')])
                ->filter(function(Builder $builder, string $value) {
                    $this->startDate = $value;
                    return $builder; // Filter diterapkan saat kalkulasi
                }),

            DateFilter::make('Sampai Tanggal')
                ->config(['max' => now()->format('Y-m-d')])
                ->filter(function(Builder $builder, string $value) {
                    $this->endDate = $value;
                    return $builder; // Filter diterapkan saat kalkulasi
                }),

            SelectFilter::make('Tipe Akun')
                ->options([
                    '' => 'Semua Tipe',
                    'Aset' => 'Aset',
                    'Kewajiban' => 'Kewajiban',
                    'Ekuitas' => 'Ekuitas',
                ])
                ->filter(function(Builder $builder, string $value) {
                    if ($value) {
                        return $builder->where('tipe_akun', $value);
                    }
                }),
        ];
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id_akun')
                ->sortable()
           ,
            Column::make('Kode Akun', 'kode_akun')
                ->sortable()
                ->searchable(),

            Column::make('Nama Akun', 'nama_akun')
                ->sortable()
                ->searchable(),

            Column::make('Tipe Akun', 'tipe_akun')
                ->sortable(),

            Column::make('Saldo Awal', 'saldo_awal')
                ->sortable()
                ->format(fn($value) => 'Rp ' . number_format($value, 0, ',', '.')),

            Column::make('Debit', 'id_akun')
                ->label(function($row) {
                    $debit = $this->sumSide($row, 'debit');
                    return 'Rp ' . number_format($debit, 0, ',', '.');
                }),

            Column::make('Kredit', 'id_akun')
                ->label(function($row) {
                    $kredit = $this->sumSide($row, 'kredit');
                    return 'Rp ' . number_format($kredit, 0, ',', '.');
                }),

            Column::make('Saldo Akhir', 'id_akun')
                ->label(function($row) {
                    $saldo = $this->calculateSaldo($row);
                    $color = $saldo < 0 ? 'text-red-600' : 'text-green-600';
                    return '<span class="' . $color . ' font-medium">Rp ' . number_format(abs($saldo), 0, ',', '.') . '</span>';
                })
                ->html(),
        ];
    }

    private function sumSide($akun, string $field): float
    {
        $query = JurnalUmum::where('id_akun', $akun->id_akun);

        if ($this->startDate) {
            $query->whereDate('tanggal', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('tanggal', '<=', $this->endDate);
        }

        return (float) $query->sum($field);
    }

    private function calculateSaldo($akun): float
    {
        $saldoAwal = $akun->saldo_awal;
        $debit = $this->sumSide($akun, 'debit');
        $kredit = $this->sumSide($akun, 'kredit');

        // Saldo normal berdasarkan tipe akun
        switch ($akun->tipe_akun) {
            case 'Aset':
                return $saldoAwal + $debit - $kredit;
            case 'Kewajiban':
            case 'Ekuitas':
                return $saldoAwal + $kredit - $debit;
            default:
                return $saldoAwal + $debit - $kredit;
        }
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
            $this->addError('export', 'Pilih akun yang akan diekspor terlebih dahulu.');
            return;
        }

        $akunQuery = Akun::whereIn('id_akun', $selected)
            ->whereIn('tipe_akun', ['Aset', 'Kewajiban', 'Ekuitas'])
            ->orderBy('kode_akun');

        return $this->generatePdfReport($akunQuery, 'selected');
    }

    public function exportAllPdf()
    {
        $akunQuery = Akun::whereIn('tipe_akun', ['Aset', 'Kewajiban', 'Ekuitas'])
            ->orderBy('kode_akun');

        return $this->generatePdfReport($akunQuery, 'all');
    }

    private function generatePdfReport($akunQuery, $type = 'selected')
    {
        $list = $akunQuery->get()->map(function($row) {
            return [
                'kode'        => $row->kode_akun,
                'nama'        => $row->nama_akun,
                'tipe'        => $row->tipe_akun,
                'saldo_awal'  => $row->saldo_awal,
                'debit'       => $this->sumSide($row, 'debit'),
                'kredit'      => $this->sumSide($row, 'kredit'),
                'saldo_akhir' => $this->calculateSaldo($row),
            ];
        });

        // Group by tipe akun
        $aset = $list->where('tipe', 'Aset');
        $kewajiban = $list->where('tipe', 'Kewajiban');
        $ekuitas = $list->where('tipe', 'Ekuitas');

        // Calculate totals
        $totalAset = $aset->sum('saldo_akhir');
        $totalKewajiban = $kewajiban->sum('saldo_akhir');
        $totalEkuitas = $ekuitas->sum('saldo_akhir');
$totalDebet = $list->sum('debit');
$totalKredit = $list->sum('kredit');
        // Format dates
        $start = $this->startDate ? Carbon::parse($this->startDate)->format('d M Y') : 'Awal';
        $end = $this->endDate ? Carbon::parse($this->endDate)->format('d M Y') : 'Sekarang';

 $pdf = Pdf::loadView('exports.neraca-pdf', [
    'aset' => $aset,
    'kewajiban' => $kewajiban,
    'ekuitas' => $ekuitas,
    'totalAset' => $totalAset,
    'totalKewajiban' => $totalKewajiban,
    'totalEkuitas' => $totalEkuitas,
    'totalDebet' => $totalDebet,     // ✅ tambahkan ini
    'totalKredit' => $totalKredit,   // ✅ dan ini
    'start' => $start,
    'end' => $end,
    'exportType' => $type,
    'list' => $list,
]);

        $this->clearSelected();

        $filename = "laporan-neraca_{$type}_{$this->startDate}_{$this->endDate}.pdf";

        return response()->streamDownload(
            fn() => print($pdf->stream()),
            $filename
        );
    }
}
