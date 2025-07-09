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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LaporanLabaRugiTable extends DataTableComponent
{
    public string $startDate = '';
    public string $endDate = '';

    protected $model = Akun::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id_akun');
        $this->setDefaultSort('kode_akun', 'asc');
        $this->setTableRowUrl(function($row) {
            return null;
        });
    }

    public function builder(): Builder
    {
        return Akun::query()
            ->whereIn('tipe_akun', ['pendapatan', 'beban']) // lowercase sesuai seeder
            ->orderBy('kode_akun', 'asc');
    }

    public function filters(): array
    {
        return [
            DateFilter::make('Dari Tanggal')
                ->config(['max' => now()->format('Y-m-d')])
                ->filter(function (Builder $builder, string $value) {
                    $this->startDate = $value;
                    return $builder;
                }),

            DateFilter::make('Sampai Tanggal')
                ->config(['max' => now()->format('Y-m-d')])
                ->filter(function (Builder $builder, string $value) {
                    $this->endDate = $value;
                    return $builder;
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

            Column::make('Saldo')
                ->label(
                    fn($row, Column $column) => $this->getSaldoValue($row, $column)
                ),
        ];
    }

    /**
     * Method untuk menghitung dan menampilkan saldo
     */
    private function getSaldoValue($row, Column $column): string
    {
        $saldo = $this->calculateSaldo($row);

        // Format currency
        return 'Rp ' . number_format($saldo, 0, ',', '.');
    }

    /**
     * Method utama untuk menghitung saldo - pendekatan sederhana
     */
    private function calculateSaldo(Akun $akun): float
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();

        // Coba dari jurnal dulu
        $saldoJurnal = $this->hitungDariJurnal($akun, $startDate, $endDate);

        // Jika saldo jurnal = 0, coba dari transaksi
        if ($saldoJurnal == 0) {
            $saldoTransaksi = $this->hitungDariTransaksi($akun, $startDate, $endDate);
            return $saldoTransaksi;
        }

        return $saldoJurnal;
    }

    /**
     * Hitung saldo dari tabel jurnal_umum
     */
    private function hitungDariJurnal(Akun $akun, string $startDate, string $endDate): float
    {
        // Ambil total debit dan kredit
        $totalDebit = JurnalUmum::where('id_akun', $akun->id_akun)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->sum('debit');

        $totalKredit = JurnalUmum::where('id_akun', $akun->id_akun)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->sum('kredit');

        // Hitung saldo berdasarkan tipe akun (sesuai seeder: lowercase)
        if ($akun->tipe_akun === 'pendapatan') {
            return $totalKredit - $totalDebit;
        } else { // beban
            return $totalDebit - $totalKredit;
        }
    }

    /**
     * Hitung saldo dari tabel transaksi (backup jika jurnal kosong)
     */
    private function hitungDariTransaksi(Akun $akun, string $startDate, string $endDate): float
    {
        $namaAkun = strtolower($akun->nama_akun);

        if ($akun->tipe_akun === 'Pendapatan') {
            // Untuk akun pendapatan
            if (str_contains($namaAkun, 'penjualan') || str_contains($namaAkun, 'pendapatan')) {
                return TransaksiPenjualan::whereBetween('tanggal_transaksi', [$startDate, $endDate])
                    ->where('status', 'selesai')
                    ->sum('total_harga');
            }
        } else {
            // Untuk akun beban
            if (str_contains($namaAkun, 'hpp') || str_contains($namaAkun, 'pokok')) {
                return TransaksiPenjualan::whereBetween('tanggal_transaksi', [$startDate, $endDate])
                    ->where('status', 'selesai')
                    ->sum('harga_pokok');
            }

            if (str_contains($namaAkun, 'operasional') || str_contains($namaAkun, 'beban')) {
                return Pengeluaran::whereBetween('tanggal', [$startDate, $endDate])
                    ->sum('jumlah');
            }
        }

        return 0;
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

    /**
     * Hitung total pendapatan untuk summary
     */
    public function getTotalPendapatan(): float
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();

        // Dari jurnal
        $pendapatanJurnal = JurnalUmum::join('akun', 'jurnal_umum.id_akun', '=', 'akun.id_akun')
            ->where('akun.tipe_akun', 'Pendapatan')
            ->whereBetween('jurnal_umum.tanggal', [$startDate, $endDate])
            ->sum(DB::raw('jurnal_umum.kredit - jurnal_umum.debit'));

        // Dari transaksi (backup)
        $pendapatanTransaksi = TransaksiPenjualan::whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->where('status', 'selesai')
            ->sum('total_harga');

        return max($pendapatanJurnal, $pendapatanTransaksi);
    }

    /**
     * Hitung total beban untuk summary
     */
    public function getTotalBeban(): float
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();

        // Dari jurnal
        $bebanJurnal = JurnalUmum::join('akun', 'jurnal_umum.id_akun', '=', 'akun.id_akun')
            ->where('akun.tipe_akun', 'Beban')
            ->whereBetween('jurnal_umum.tanggal', [$startDate, $endDate])
            ->sum(DB::raw('jurnal_umum.debit - jurnal_umum.kredit'));

        // Dari transaksi (backup)
        $hpp = TransaksiPenjualan::whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->where('status', 'selesai')
            ->sum('harga_pokok');

        $operasional = Pengeluaran::whereBetween('tanggal', [$startDate, $endDate])
            ->sum('jumlah');

        return max($bebanJurnal, $hpp + $operasional);
    }

    /**
     * Hitung laba rugi
     */
    public function getLabaRugi(): float
    {
        return $this->getTotalPendapatan() - $this->getTotalBeban();
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
            ->whereIn('tipe_akun', ['pendapatan', 'beban']) // lowercase
            ->orderBy('kode_akun')
            ->get();

        return $this->generatePdf($akuns, 'selected');
    }

    public function exportAllPdf()
    {
        $akuns = Akun::whereIn('tipe_akun', ['pendapatan', 'beban']) // lowercase
            ->orderBy('kode_akun')
            ->get();

        return $this->generatePdf($akuns, 'all');
    }

    private function generatePdf(Collection $akuns, string $type = 'all')
    {
        // Kelompokkan akun berdasarkan tipe
        $pendapatanAkuns = $akuns->where('tipe_akun', 'pendapatan'); // lowercase
        $bebanAkuns = $akuns->where('tipe_akun', 'beban'); // lowercase

        // Hitung data untuk laporan
        $pendapatanData = $pendapatanAkuns->map(function($akun) {
            return [
                'kode' => $akun->kode_akun,
                'nama' => $akun->nama_akun,
                'jumlah' => $this->calculateSaldo($akun),
            ];
        });

        $bebanData = $bebanAkuns->map(function($akun) {
            return [
                'kode' => $akun->kode_akun,
                'nama' => $akun->nama_akun,
                'jumlah' => $this->calculateSaldo($akun),
            ];
        });

        // Hitung total KONSISTEN dengan data yang ditampilkan
        $totalPendapatanFromData = $pendapatanData->sum('jumlah');
        $totalBebanFromData = $bebanData->sum('jumlah');

        // Jika tidak ada data di akun, coba ambil dari transaksi langsung
        $totalPendapatanFromTransaksi = $this->getTotalPendapatan();
        $totalBebanFromTransaksi = $this->getTotalBeban();

        // Gunakan yang lebih besar (prioritas ke data akun)
        $totalPendapatan = $totalPendapatanFromData > 0 ? $totalPendapatanFromData : $totalPendapatanFromTransaksi;
        $totalBeban = $totalBebanFromData > 0 ? $totalBebanFromData : $totalBebanFromTransaksi;

        $labaRugi = $totalPendapatan - $totalBeban;

        // Tambahkan data transaksi langsung jika akun kosong
        $pendapatanTransaksiLangsung = [];
        $bebanTransaksiLangsung = [];

        if ($totalPendapatanFromData == 0 && $totalPendapatanFromTransaksi > 0) {
            $pendapatanTransaksiLangsung[] = [
                'kode' => '4001',
                'nama' => 'Pendapatan dari Transaksi',
                'jumlah' => $totalPendapatanFromTransaksi,
            ];
        }

        if ($totalBebanFromData == 0 && $totalBebanFromTransaksi > 0) {
            $bebanTransaksiLangsung[] = [
                'kode' => '5001',
                'nama' => 'Beban dari Transaksi',
                'jumlah' => $totalBebanFromTransaksi,
            ];
        }

        // Format tanggal
        $startDate = $this->startDate ? Carbon::parse($this->startDate)->format('d M Y') : 'Awal Tahun';
        $endDate = $this->endDate ? Carbon::parse($this->endDate)->format('d M Y') : 'Hari Ini';

        $pdf = Pdf::loadView('exports.laba-rugi-pdf', [
            'pendapatanData' => $pendapatanData,
            'bebanData' => $bebanData,
            'pendapatanTransaksiLangsung' => collect($pendapatanTransaksiLangsung),
            'bebanTransaksiLangsung' => collect($bebanTransaksiLangsung),
            'totalPendapatan' => $totalPendapatan,
            'totalBeban' => $totalBeban,
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
