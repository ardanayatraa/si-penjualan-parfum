<?php

namespace App\Livewire\Table;

use App\Models\Akun;
use App\Models\JurnalUmum;
use App\Models\TransaksiPenjualan;
use App\Models\Pengeluaran;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class LaporanLabaRugiTable extends DataTableComponent
{
    public string $startDate = '';
    public string $endDate   = '';

    protected $model = Akun::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id_akun');
        $this->setDefaultSort('kode_akun', 'asc');
        $this->setTableRowUrl(function($row) {
            return null; // Disable row clicks
        });
    }

    public function builder(): Builder
    {
        // Hanya ambil akun Pendapatan dan Beban
        return Akun::query()
            ->whereIn('tipe_akun', ['Pendapatan', 'Beban'])
            ->orderBy('kode_akun', 'asc');
    }

    public function filters(): array
    {
        return [
            DateFilter::make('Dari Tanggal')
                ->config(['max' => now()->format('Y-m-d')])
                ->filter(function (Builder $builder, string $value) {
                    $this->startDate = $value;
                    return $builder; // Filter tidak diterapkan di builder karena kita hitung manual
                }),

            DateFilter::make('Sampai Tanggal')
                ->config(['max' => now()->format('Y-m-d')])
                ->filter(function (Builder $builder, string $value) {
                    $this->endDate = $value;
                    return $builder; // Filter tidak diterapkan di builder karena kita hitung manual
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

            Column::make('Saldo', 'id_akun')
               ,
        ];
    }

    /**
     * Hitung saldo akun berdasarkan periode yang dipilih
     */
    private function calculateAccountBalance(Akun $akun): float
    {
        $startDate = $this->startDate ?: '1900-01-01';
        $endDate = $this->endDate ?: now()->format('Y-m-d');

        // Saldo dari jurnal umum
        $jurnalQuery = JurnalUmum::where('id_akun', $akun->id_akun)
            ->whereBetween('tanggal', [$startDate, $endDate]);

        $totalDebit = $jurnalQuery->sum('debit');
        $totalKredit = $jurnalQuery->sum('kredit');

        // Untuk akun Pendapatan: saldo = kredit - debit
        // Untuk akun Beban: saldo = debit - kredit
        if ($akun->tipe_akun === 'Pendapatan') {
            return $totalKredit - $totalDebit;
        } else {
            return $totalDebit - $totalKredit;
        }
    }

    /**
     * Hitung pendapatan dari penjualan
     */
    private function calculateRevenue(): float
    {
        $startDate = $this->startDate ?: '1900-01-01';
        $endDate = $this->endDate ?: now()->format('Y-m-d');

        return TransaksiPenjualan::whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->where('status', 'selesai')
            ->sum('total_harga');
    }

    /**
     * Hitung total pengeluaran
     */
    private function calculateExpenses(): float
    {
        $startDate = $this->startDate ?: '1900-01-01';
        $endDate = $this->endDate ?: now()->format('Y-m-d');

        return Pengeluaran::whereBetween('tanggal', [$startDate, $endDate])
            ->sum('jumlah');
    }

    /**
     * Hitung HPP (Harga Pokok Penjualan)
     */
    private function calculateCOGS(): float
    {
        $startDate = $this->startDate ?: '1900-01-01';
        $endDate = $this->endDate ?: now()->format('Y-m-d');

        return TransaksiPenjualan::whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->where('status', 'selesai')
            ->sum('harga_pokok');
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

        $akuns = Akun::whereIn('id_akun', $selected)
            ->whereIn('tipe_akun', ['Pendapatan', 'Beban'])
            ->orderBy('kode_akun')
            ->get();

        return $this->generatePdf($akuns, 'selected');
    }

    public function exportAllPdf()
    {
        $akuns = Akun::whereIn('tipe_akun', ['Pendapatan', 'Beban'])
            ->orderBy('kode_akun')
            ->get();

        return $this->generatePdf($akuns, 'all');
    }

    private function generatePdf(Collection $akuns, string $type = 'all')
    {
        // Kelompokkan akun berdasarkan tipe
        $pendapatanAkuns = $akuns->where('tipe_akun', 'Pendapatan');
        $bebanAkuns = $akuns->where('tipe_akun', 'Beban');

        // Hitung data untuk laporan
        $pendapatanData = $pendapatanAkuns->map(function($akun) {
            return [
                'kode' => $akun->kode_akun,
                'nama' => $akun->nama_akun,
                'jumlah' => $this->calculateAccountBalance($akun),
            ];
        });

        $bebanData = $bebanAkuns->map(function($akun) {
            return [
                'kode' => $akun->kode_akun,
                'nama' => $akun->nama_akun,
                'jumlah' => $this->calculateAccountBalance($akun),
            ];
        });

        // Hitung total dan laba rugi
        $totalPendapatan = $pendapatanData->sum('jumlah');
        $totalBeban = $bebanData->sum('jumlah');

        // Tambahkan pendapatan dari penjualan langsung
        $pendapatanPenjualan = $this->calculateRevenue();

        // Tambahkan HPP dan pengeluaran operasional
        $hpp = $this->calculateCOGS();
        $pengeluaranOperasional = $this->calculateExpenses();

        $totalPendapatanBersih = $totalPendapatan + $pendapatanPenjualan;
        $totalBebanBersih = $totalBeban + $hpp + $pengeluaranOperasional;
        $labaRugi = $totalPendapatanBersih - $totalBebanBersih;

        // Format tanggal
        $startDate = $this->startDate ? Carbon::parse($this->startDate)->format('d M Y') : 'Awal';
        $endDate = $this->endDate ? Carbon::parse($this->endDate)->format('d M Y') : 'Sekarang';

        $pdf = Pdf::loadView('exports.laba-rugi-pdf', [
            'pendapatanData' => $pendapatanData,
            'bebanData' => $bebanData,
            'pendapatanPenjualan' => $pendapatanPenjualan,
            'hpp' => $hpp,
            'pengeluaranOperasional' => $pengeluaranOperasional,
            'totalPendapatan' => $totalPendapatanBersih,
            'totalBeban' => $totalBebanBersih,
            'labaRugi' => $labaRugi,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'exportType' => $type,
            'rawStartDate' => $this->startDate ?: '',
            'rawEndDate' => $this->endDate ?: '',
        ])->setPaper('a4', 'portrait');

        $this->clearSelected();

        $filename = "laporan-laba-rugi_{$type}_" .
                   ($this->startDate ?: 'start') . "_" .
                   ($this->endDate ?: 'end') . ".pdf";

        return response()->streamDownload(
            fn() => print($pdf->stream()),
            $filename
        );
    }
}
