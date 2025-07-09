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
    public string $endDate = '';

    protected $model = Akun::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id_akun');
        $this->setDefaultSort('kode_akun', 'asc');
    }

    public function builder(): Builder
    {
        return Akun::query()
            ->whereIn('tipe_akun', ['aset', 'kewajiban']) // Sesuai seeder: lowercase
            ->orderBy('kode_akun');
    }

    public function filters(): array
    {
        return [
            DateFilter::make('Dari Tanggal')
                ->config(['max' => now()->format('Y-m-d')])
                ->filter(function(Builder $builder, string $value) {
                    $this->startDate = $value;
                    return $builder;
                }),

            DateFilter::make('Sampai Tanggal')
                ->config(['max' => now()->format('Y-m-d')])
                ->filter(function(Builder $builder, string $value) {
                    $this->endDate = $value;
                    return $builder;
                }),

            SelectFilter::make('Tipe Akun')
                ->options([
                    '' => 'Semua Tipe',
                    'aset' => 'Aset',
                    'kewajiban' => 'Kewajiban',
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
            Column::make('ID', 'id_akun')->sortable(),

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

            Column::make('Debit')
                ->label(
                    fn($row, Column $column) => $this->getDebitValue($row, $column)
                ),

            Column::make('Kredit')
                ->label(
                    fn($row, Column $column) => $this->getKreditValue($row, $column)
                ),

            Column::make('Saldo Akhir')
                ->label(
                    fn($row, Column $column) => $this->getSaldoAkhirValue($row, $column)
                )
                ->html(),
        ];
    }

    /**
     * Get nilai debit untuk kolom
     */
    private function getDebitValue($row, Column $column): string
    {
        $debit = $this->sumSide($row, 'debit');
        return 'Rp ' . number_format($debit, 0, ',', '.');
    }

    /**
     * Get nilai kredit untuk kolom
     */
    private function getKreditValue($row, Column $column): string
    {
        $kredit = $this->sumSide($row, 'kredit');
        return 'Rp ' . number_format($kredit, 0, ',', '.');
    }

    /**
     * Get nilai saldo akhir untuk kolom dengan styling
     */
    private function getSaldoAkhirValue($row, Column $column): string
    {
        $saldo = $this->calculateSaldo($row);
        $color = $saldo < 0 ? 'text-red-600' : 'text-green-600';
        return '<span class="' . $color . ' font-medium">Rp ' . number_format(abs($saldo), 0, ',', '.') . '</span>';
    }

    /**
     * Hitung total debit/kredit dari jurnal berdasarkan periode
     */
    private function sumSide($akun, string $field): float
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();

        $query = JurnalUmum::where('id_akun', $akun->id_akun)
            ->whereBetween('tanggal', [$startDate, $endDate]);

        return (float) $query->sum($field);
    }

    /**
     * Hitung saldo akhir akun
     */
    private function calculateSaldo($akun): float
    {
        $saldoAwal = $akun->saldo_awal;
        $debit = $this->sumSide($akun, 'debit');
        $kredit = $this->sumSide($akun, 'kredit');

        // Saldo normal berdasarkan tipe akun (sesuai seeder)
        switch ($akun->tipe_akun) {
            case 'aset': // lowercase sesuai seeder
                return $saldoAwal + $debit - $kredit;
            case 'kewajiban': // lowercase sesuai seeder
                return $saldoAwal + $kredit - $debit;
            default:
                return $saldoAwal + $debit - $kredit;
        }
    }

    /**
     * Get start date dengan default
     */
    private function getStartDate(): string
    {
        return $this->startDate ?: Carbon::now()->startOfYear()->format('Y-m-d');
    }

    /**
     * Get end date dengan default
     */
    private function getEndDate(): string
    {
        return $this->endDate ?: Carbon::now()->format('Y-m-d');
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
            ->whereIn('tipe_akun', ['aset', 'kewajiban']) // lowercase
            ->orderBy('kode_akun');

        return $this->generatePdfReport($akunQuery, 'selected');
    }

    public function exportAllPdf()
    {
        $akunQuery = Akun::whereIn('tipe_akun', ['aset', 'kewajiban']) // lowercase
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

        // Group by tipe akun (sesuai seeder)
        $aset = $list->where('tipe', 'aset');
        $kewajiban = $list->where('tipe', 'kewajiban');

        // Calculate totals
        $totalAset = $aset->sum('saldo_akhir');
        $totalKewajiban = $kewajiban->sum('saldo_akhir');
        $totalDebet = $list->sum('debit');
        $totalKredit = $list->sum('kredit');

        // Format dates
        $start = $this->startDate ? Carbon::parse($this->startDate)->format('d M Y') : 'Awal Tahun';
        $end = $this->endDate ? Carbon::parse($this->endDate)->format('d M Y') : 'Hari Ini';

        $pdf = Pdf::loadView('exports.neraca-pdf', [
            'aset' => $aset,
            'kewajiban' => $kewajiban,
            'totalAset' => $totalAset,
            'totalKewajiban' => $totalKewajiban,
            'totalDebet' => $totalDebet,
            'totalKredit' => $totalKredit,
            'start' => $start,
            'end' => $end,
            'exportType' => $type,
            'list' => $list,
            'rawStartDate' => $this->startDate ?: '',
            'rawEndDate' => $this->endDate ?: '',
        ])->setPaper('a4', 'landscape');

        $this->clearSelected();

        $filename = "laporan-neraca_{$type}_" .
                   ($this->startDate ?: 'start') . "_" .
                   ($this->endDate ?: 'end') . ".pdf";

        return response()->streamDownload(
            fn() => print($pdf->stream()),
            $filename
        );
    }
}
