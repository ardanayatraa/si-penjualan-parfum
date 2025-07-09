<?php

namespace App\Livewire\Table;

use App\Models\Akun;
use App\Models\JurnalUmum;
use App\Models\TransaksiPenjualan;
use App\Models\Pengeluaran;
use App\Models\DetailTransaksiPenjualan;
use App\Models\Produk;
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
    public string $calculationMethod = 'hybrid'; // jurnal, transaction, hybrid

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
            Column::make('Kode Akun', 'kode_akun')->sortable()->searchable(),
            Column::make('Nama Akun', 'nama_akun')->sortable()->searchable(),
            Column::make('Tipe Akun', 'tipe_akun')->sortable(),
            Column::make('Saldo', 'id_akun')
                ->format(function($value, $row) {
                    $saldo = $this->calculateAccountBalanceHybrid($row);
                    return $saldo > 0 ? 'Rp ' . number_format($saldo, 0, ',', '.') : 'Rp 0';
                }),
        ];
    }

    // =================== PENDEKATAN 1: JURNAL BASED (ORIGINAL) ===================
    private function calculateAccountBalanceJurnal(Akun $akun): float
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();

        $jurnalQuery = JurnalUmum::where('id_akun', $akun->id_akun)
            ->whereBetween('tanggal', [$startDate, $endDate]);

        $totalDebit = $jurnalQuery->sum('debit') ?: 0;
        $totalKredit = $jurnalQuery->sum('kredit') ?: 0;

        if ($akun->tipe_akun === 'Pendapatan') {
            return $totalKredit - $totalDebit;
        } else {
            return $totalDebit - $totalKredit;
        }
    }

    // =================== PENDEKATAN 2: TRANSACTION BASED ===================
    private function calculateAccountBalanceTransaction(Akun $akun): float
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();

        // Mapping akun ke perhitungan transaksi
        $saldo = 0;

        switch ($akun->tipe_akun) {
            case 'Pendapatan':
                $saldo = $this->calculateRevenueByAccount($akun, $startDate, $endDate);
                break;
            case 'Beban':
                $saldo = $this->calculateExpenseByAccount($akun, $startDate, $endDate);
                break;
        }

        return $saldo;
    }

    private function calculateRevenueByAccount(Akun $akun, string $startDate, string $endDate): float
    {
        // Pendekatan berdasarkan nama/kode akun
        $kodeAkun = strtolower($akun->kode_akun);
        $namaAkun = strtolower($akun->nama_akun);

        // Pendapatan Penjualan
        if (str_contains($namaAkun, 'penjualan') || str_contains($kodeAkun, '4-1')) {
            return TransaksiPenjualan::whereBetween('tanggal_transaksi', [$startDate, $endDate])
                ->where('status', 'selesai')
                ->sum('total_harga');
        }

        // Pendapatan Jasa
        if (str_contains($namaAkun, 'jasa') || str_contains($kodeAkun, '4-2')) {
            return TransaksiPenjualan::whereBetween('tanggal_transaksi', [$startDate, $endDate])
                ->where('status', 'selesai')
                ->where('jenis_transaksi', 'jasa')
                ->sum('total_harga');
        }

        // Pendapatan Lain-lain
        if (str_contains($namaAkun, 'lain') || str_contains($kodeAkun, '4-9')) {
            return Pengeluaran::whereBetween('tanggal', [$startDate, $endDate])
                ->where('jenis', 'pendapatan_lain')
                ->sum('jumlah');
        }

        return 0;
    }

    private function calculateExpenseByAccount(Akun $akun, string $startDate, string $endDate): float
    {
        $kodeAkun = strtolower($akun->kode_akun);
        $namaAkun = strtolower($akun->nama_akun);

        // HPP (Harga Pokok Penjualan)
        if (str_contains($namaAkun, 'hpp') || str_contains($namaAkun, 'pokok') || str_contains($kodeAkun, '5-1')) {
            return TransaksiPenjualan::whereBetween('tanggal_transaksi', [$startDate, $endDate])
                ->where('status', 'selesai')
                ->sum('harga_pokok');
        }

        // Beban Operasional
        if (str_contains($namaAkun, 'operasional') || str_contains($kodeAkun, '5-2')) {
            return Pengeluaran::whereBetween('tanggal', [$startDate, $endDate])
                ->where('kategori', 'operasional')
                ->sum('jumlah');
        }

        // Beban Gaji
        if (str_contains($namaAkun, 'gaji') || str_contains($namaAkun, 'upah') || str_contains($kodeAkun, '5-3')) {
            return Pengeluaran::whereBetween('tanggal', [$startDate, $endDate])
                ->where('kategori', 'gaji')
                ->sum('jumlah');
        }

        // Beban Sewa
        if (str_contains($namaAkun, 'sewa') || str_contains($kodeAkun, '5-4')) {
            return Pengeluaran::whereBetween('tanggal', [$startDate, $endDate])
                ->where('kategori', 'sewa')
                ->sum('jumlah');
        }

        // Beban Utilitas (Listrik, Air, dll)
        if (str_contains($namaAkun, 'listrik') || str_contains($namaAkun, 'air') ||
            str_contains($namaAkun, 'utilitas') || str_contains($kodeAkun, '5-5')) {
            return Pengeluaran::whereBetween('tanggal', [$startDate, $endDate])
                ->whereIn('kategori', ['listrik', 'air', 'utilitas'])
                ->sum('jumlah');
        }

        // Beban Umum & Administrasi
        if (str_contains($namaAkun, 'administrasi') || str_contains($namaAkun, 'umum') || str_contains($kodeAkun, '5-6')) {
            return Pengeluaran::whereBetween('tanggal', [$startDate, $endDate])
                ->where('kategori', 'administrasi')
                ->sum('jumlah');
        }

        // Default: ambil dari pengeluaran umum
        return Pengeluaran::whereBetween('tanggal', [$startDate, $endDate])
            ->where('keterangan', 'like', '%' . $akun->nama_akun . '%')
            ->sum('jumlah');
    }

    // =================== PENDEKATAN 3: HYBRID (KOMBINASI) ===================
    private function calculateAccountBalanceHybrid(Akun $akun): float
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();

        // Cek dulu apakah ada data di jurnal
        $jurnalCount = JurnalUmum::where('id_akun', $akun->id_akun)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->count();

        if ($jurnalCount > 0) {
            // Jika ada data di jurnal, gunakan jurnal
            return $this->calculateAccountBalanceJurnal($akun);
        } else {
            // Jika tidak ada data di jurnal, hitung dari transaksi
            return $this->calculateAccountBalanceTransaction($akun);
        }
    }

    // =================== PENDEKATAN 4: AGGREGATED CALCULATION ===================
    private function calculateAccountBalanceAggregated(Akun $akun): float
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();

        // Gunakan single query dengan joins untuk performa lebih baik
        $result = DB::table('akun as a')
            ->leftJoin('jurnal_umum as j', function($join) use ($startDate, $endDate) {
                $join->on('a.id_akun', '=', 'j.id_akun')
                     ->whereBetween('j.tanggal', [$startDate, $endDate]);
            })
            ->leftJoin('transaksi_penjualan as t', function($join) use ($startDate, $endDate) {
                $join->whereBetween('t.tanggal_transaksi', [$startDate, $endDate])
                     ->where('t.status', 'selesai');
            })
            ->leftJoin('pengeluaran as p', function($join) use ($startDate, $endDate) {
                $join->whereBetween('p.tanggal', [$startDate, $endDate]);
            })
            ->where('a.id_akun', $akun->id_akun)
            ->select(
                DB::raw('COALESCE(SUM(j.debit), 0) as total_debit'),
                DB::raw('COALESCE(SUM(j.kredit), 0) as total_kredit'),
                DB::raw('COALESCE(SUM(t.total_harga), 0) as total_penjualan'),
                DB::raw('COALESCE(SUM(t.harga_pokok), 0) as total_hpp'),
                DB::raw('COALESCE(SUM(p.jumlah), 0) as total_pengeluaran')
            )
            ->first();

        // Hitung saldo berdasarkan tipe akun
        if ($akun->tipe_akun === 'Pendapatan') {
            $saldoJurnal = $result->total_kredit - $result->total_debit;
            $saldoTransaksi = $result->total_penjualan;
            return max($saldoJurnal, $saldoTransaksi); // Ambil yang lebih besar
        } else {
            $saldoJurnal = $result->total_debit - $result->total_kredit;
            $saldoTransaksi = $result->total_hpp + $result->total_pengeluaran;
            return max($saldoJurnal, $saldoTransaksi); // Ambil yang lebih besar
        }
    }

    // =================== PENDEKATAN 5: DETAILED CALCULATION ===================
    private function calculateAccountBalanceDetailed(Akun $akun): float
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();

        $calculator = new DetailedAccountCalculator($akun, $startDate, $endDate);
        return $calculator->calculate();
    }

    // =================== HELPER METHODS ===================
    private function getStartDate(): string
    {
        return $this->startDate ?: Carbon::now()->subYear()->format('Y-m-d');
    }

    private function getEndDate(): string
    {
        return $this->endDate ?: Carbon::now()->format('Y-m-d');
    }

    // =================== SUMMARY CALCULATIONS ===================
    public function getSummaryData(): array
    {
        $pendapatanAkuns = Akun::whereIn('tipe_akun', ['Pendapatan'])->get();
        $bebanAkuns = Akun::whereIn('tipe_akun', ['Beban'])->get();

        $totalPendapatan = $pendapatanAkuns->sum(function($akun) {
            return $this->calculateAccountBalanceHybrid($akun);
        });

        $totalBeban = $bebanAkuns->sum(function($akun) {
            return $this->calculateAccountBalanceHybrid($akun);
        });

        // Tambahan dari transaksi langsung
        $pendapatanLangsung = $this->calculateDirectRevenue();
        $bebanLangsung = $this->calculateDirectExpenses();

        return [
            'total_pendapatan' => $totalPendapatan + $pendapatanLangsung,
            'total_beban' => $totalBeban + $bebanLangsung,
            'laba_rugi' => ($totalPendapatan + $pendapatanLangsung) - ($totalBeban + $bebanLangsung),
            'pendapatan_breakdown' => [
                'dari_akun' => $totalPendapatan,
                'dari_transaksi' => $pendapatanLangsung,
            ],
            'beban_breakdown' => [
                'dari_akun' => $totalBeban,
                'dari_transaksi' => $bebanLangsung,
            ]
        ];
    }

    private function calculateDirectRevenue(): float
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();

        return TransaksiPenjualan::whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->where('status', 'selesai')
            ->sum('total_harga');
    }

    private function calculateDirectExpenses(): float
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();

        $hpp = TransaksiPenjualan::whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->where('status', 'selesai')
            ->sum('harga_pokok');

        $operasional = Pengeluaran::whereBetween('tanggal', [$startDate, $endDate])
            ->sum('jumlah');

        return $hpp + $operasional;
    }

    // =================== EXPORT METHODS ===================
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
        $pendapatanAkuns = $akuns->where('tipe_akun', 'Pendapatan');
        $bebanAkuns = $akuns->where('tipe_akun', 'Beban');

        $pendapatanData = $pendapatanAkuns->map(function($akun) {
            return [
                'kode' => $akun->kode_akun,
                'nama' => $akun->nama_akun,
                'jumlah' => $this->calculateAccountBalanceHybrid($akun),
            ];
        });

        $bebanData = $bebanAkuns->map(function($akun) {
            return [
                'kode' => $akun->kode_akun,
                'nama' => $akun->nama_akun,
                'jumlah' => $this->calculateAccountBalanceHybrid($akun),
            ];
        });

        $summaryData = $this->getSummaryData();

        $startDate = $this->startDate ? Carbon::parse($this->startDate)->format('d M Y') : 'Awal';
        $endDate = $this->endDate ? Carbon::parse($this->endDate)->format('d M Y') : 'Sekarang';

        $pdf = Pdf::loadView('exports.laba-rugi-pdf', [
            'pendapatanData' => $pendapatanData,
            'bebanData' => $bebanData,
            'summaryData' => $summaryData,
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

// =================== SEPARATE CALCULATOR CLASS ===================
class DetailedAccountCalculator
{
    private Akun $akun;
    private string $startDate;
    private string $endDate;

    public function __construct(Akun $akun, string $startDate, string $endDate)
    {
        $this->akun = $akun;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function calculate(): float
    {
        // Log untuk debugging
        Log::info("Calculating balance for account: {$this->akun->nama_akun}", [
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'account_type' => $this->akun->tipe_akun
        ]);

        // Coba beberapa pendekatan
        $methods = [
            'jurnal' => $this->calculateFromJurnal(),
            'transaction' => $this->calculateFromTransaction(),
            'direct' => $this->calculateFromDirectQuery(),
        ];

        Log::info("Calculation methods results", $methods);

        // Pilih nilai yang tidak nol, atau yang terbesar
        $validResults = array_filter($methods, fn($value) => $value > 0);

        if (empty($validResults)) {
            return 0;
        }

        return max($validResults);
    }

    private function calculateFromJurnal(): float
    {
        $query = JurnalUmum::where('id_akun', $this->akun->id_akun)
            ->whereBetween('tanggal', [$this->startDate, $this->endDate]);

        $totalDebit = $query->sum('debit') ?: 0;
        $totalKredit = $query->sum('kredit') ?: 0;

        if ($this->akun->tipe_akun === 'Pendapatan') {
            return $totalKredit - $totalDebit;
        } else {
            return $totalDebit - $totalKredit;
        }
    }

    private function calculateFromTransaction(): float
    {
        if ($this->akun->tipe_akun === 'Pendapatan') {
            return TransaksiPenjualan::whereBetween('tanggal_transaksi', [$this->startDate, $this->endDate])
                ->where('status', 'selesai')
                ->sum('total_harga');
        } else {
            $hpp = TransaksiPenjualan::whereBetween('tanggal_transaksi', [$this->startDate, $this->endDate])
                ->where('status', 'selesai')
                ->sum('harga_pokok');

            $pengeluaran = Pengeluaran::whereBetween('tanggal', [$this->startDate, $this->endDate])
                ->sum('jumlah');

            return $hpp + $pengeluaran;
        }
    }

    private function calculateFromDirectQuery(): float
    {
        // Query langsung ke database dengan raw SQL jika diperlukan
        $accountId = $this->akun->id_akun;

        if ($this->akun->tipe_akun === 'Pendapatan') {
            $result = DB::select("
                SELECT
                    COALESCE(SUM(kredit), 0) - COALESCE(SUM(debit), 0) as saldo
                FROM jurnal_umum
                WHERE id_akun = ?
                AND tanggal BETWEEN ? AND ?
            ", [$accountId, $this->startDate, $this->endDate]);
        } else {
            $result = DB::select("
                SELECT
                    COALESCE(SUM(debit), 0) - COALESCE(SUM(kredit), 0) as saldo
                FROM jurnal_umum
                WHERE id_akun = ?
                AND tanggal BETWEEN ? AND ?
            ", [$accountId, $this->startDate, $this->endDate]);
        }

        return $result[0]->saldo ?? 0;
    }
}
