<?php

namespace App\Livewire\Table;

use App\Models\TransaksiPenjualan;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
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
        $this->setDefaultSort('tanggal_transaksi', 'desc');
    }

    public function builder(): Builder
    {
        return TransaksiPenjualan::query()
            ->with(['kasir', 'barang', 'pajak'])
            ->where('status', 'selesai'); // Hanya transaksi selesai untuk laporan
    }

    public function filters(): array
    {
        return [
            DateFilter::make('Tanggal Mulai')
                ->config(['max' => now()->format('Y-m-d')])
                ->filter(function(Builder $builder, string $value) {
                    $this->startDate = $value;
                    return $builder->whereDate('tanggal_transaksi', '>=', $value);
                }),

            DateFilter::make('Tanggal Akhir')
                ->config(['max' => now()->format('Y-m-d')])
                ->filter(function(Builder $builder, string $value) {
                    $this->endDate = $value;
                    return $builder->whereDate('tanggal_transaksi', '<=', $value);
                }),

            SelectFilter::make('Metode Pembayaran')
                ->options([
                    '' => 'Semua Metode',
                    'cash' => 'Tunai',
                    'transfer' => 'Transfer Bank',
                    'debit_card' => 'Kartu Debit',
                    'credit_card' => 'Kartu Kredit',
                    'e_wallet' => 'E-Wallet',
                ])
                ->filter(function(Builder $builder, string $value) {
                    if ($value) {
                        return $builder->where('metode_pembayaran', $value);
                    }
                }),
        ];
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
            $this->addError('export', 'Pilih transaksi yang akan diekspor terlebih dahulu.');
            return;
        }

        $query = TransaksiPenjualan::with(['kasir', 'barang', 'pajak'])
            ->where('status', 'selesai')
            ->whereIn('id', $selected);

        return $this->generatePdfReport($query, 'selected');
    }

    public function exportAllPdf()
    {
        $query = TransaksiPenjualan::with(['kasir', 'barang', 'pajak'])
            ->where('status', 'selesai');

        return $this->generatePdfReport($query, 'all');
    }

    private function generatePdfReport($query, $type = 'selected')
    {
        // Apply date filters if exists
        if ($this->startDate) {
            $query->whereDate('tanggal_transaksi', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('tanggal_transaksi', '<=', $this->endDate);
        }

        // Validate date range
        if ($this->startDate && $this->endDate) {
            $this->validate([
                'startDate' => 'required|date',
                'endDate'   => 'required|date|after_or_equal:startDate',
            ]);
        }

        $data = $query->orderBy('tanggal_transaksi', 'desc')->get();

        if ($data->isEmpty()) {
            $this->addError('export', 'Tidak ada data untuk diekspor.');
            return;
        }

        // Calculate totals and statistics
        $totalTransaksi = $data->count();
        $totalJumlahTerjual = $data->sum('jumlah_terjual');
        $totalSubtotal = $data->sum('subtotal');
        $totalPajak = $data->sum(function($item) {
            if ($item->pajak) {
                return ($item->subtotal * $item->pajak->presentase) / 100;
            }
            return 0;
        });
        $totalHarga = $data->sum('total_harga');
        $totalLabaBruto = $data->sum('laba_bruto');
        $totalHargaPokok = $data->sum(function($item) {
            return $item->harga_pokok * $item->jumlah_terjual;
        });

        // Calculate date range
        $start = $this->startDate
            ? Carbon::parse($this->startDate)->format('d M Y')
            : ($data->min('tanggal_transaksi')?->format('d M Y') ?? now()->format('d M Y'));

        $end = $this->endDate
            ? Carbon::parse($this->endDate)->format('d M Y')
            : ($data->max('tanggal_transaksi')?->format('d M Y') ?? now()->format('d M Y'));

        // Group by payment method for analysis
        $paymentMethodStats = $data->groupBy('metode_pembayaran')->map(function($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('total_harga'),
                'percentage' => 0, // Will be calculated below
            ];
        });

        // Calculate percentages
        $totalRevenue = $paymentMethodStats->sum('total');
        $paymentMethodStats = $paymentMethodStats->map(function($stat) use ($totalRevenue) {
            $stat['percentage'] = $totalRevenue > 0 ? ($stat['total'] / $totalRevenue) * 100 : 0;
            return $stat;
        });

        // Group by kasir for analysis
        $kasirStats = $data->groupBy('kasir.username')->map(function($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('total_harga'),
                'laba' => $group->sum('laba_bruto'),
            ];
        });

        $pdf = Pdf::loadView('exports.penjualan-pdf', [
            'data' => $data,
            'start' => $start,
            'end' => $end,
            'exportType' => $type,
            'statistics' => [
                'totalTransaksi' => $totalTransaksi,
                'totalJumlahTerjual' => $totalJumlahTerjual,
                'totalSubtotal' => $totalSubtotal,
                'totalPajak' => $totalPajak,
                'totalHarga' => $totalHarga,
                'totalLabaBruto' => $totalLabaBruto,
                'totalHargaPokok' => $totalHargaPokok,
                'rataRataPerTransaksi' => $totalTransaksi > 0 ? $totalHarga / $totalTransaksi : 0,
                'marginLaba' => $totalSubtotal > 0 ? ($totalLabaBruto / $totalSubtotal) * 100 : 0,
            ],
            'paymentMethodStats' => $paymentMethodStats,
            'kasirStats' => $kasirStats,
        ])->setPaper('a4', 'landscape');

        $this->clearSelected();

        $filename = "laporan-penjualan_{$type}_{$this->startDate}_{$this->endDate}.pdf";

        return response()->streamDownload(
            fn() => print($pdf->stream()),
            $filename
        );
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable(),

            Column::make('Kode', 'id')
                ->label(fn($row) => 'PNJ-' . str_pad($row->id, 6, '0', STR_PAD_LEFT))
                ->sortable(),

            Column::make('Tanggal', 'tanggal_transaksi')
                ->sortable()
                ->format(fn($value) => Carbon::parse($value)->format('d-m-Y H:i')),

            Column::make('Kasir', 'kasir.username')
                ->sortable()
                ->searchable(),

            Column::make('Barang', 'barang.nama_barang')
                ->sortable()
                ->searchable(),

            Column::make('Jumlah', 'jumlah_terjual') // Updated field name
                ->sortable(),

            Column::make('Subtotal', 'subtotal')
                ->sortable()
                ->format(fn($value) => 'Rp ' . number_format($value, 0, ',', '.')),

            Column::make('Pajak', 'id')
                ->label(function($row) {
                    if ($row->pajak) {
                        $pajakAmount = ($row->subtotal * $row->pajak->presentase) / 100;
                        return 'Rp ' . number_format($pajakAmount, 0, ',', '.');
                    }
                    return 'Rp 0';
                }),

            Column::make('Total Harga', 'total_harga')
                ->sortable()
                ->format(fn($value) => 'Rp ' . number_format($value, 0, ',', '.')),

            Column::make('HPP', 'id') // Harga Pokok Penjualan
                ->label(function($row) {
                    $hpp = $row->harga_pokok * $row->jumlah_terjual;
                    return 'Rp ' . number_format($hpp, 0, ',', '.');
                }),

            Column::make('Laba Bruto', 'laba_bruto') // New field
                ->sortable()
                ->format(fn($value) => 'Rp ' . number_format($value, 0, ',', '.')),

            Column::make('Pembayaran', 'metode_pembayaran') // New field
                ->label(function($row) {
                    $methods = [
                        'cash' => 'Tunai',
                        'transfer' => 'Transfer',
                        'debit_card' => 'Kartu Debit',
                        'credit_card' => 'Kartu Kredit',
                        'e_wallet' => 'E-Wallet',
                    ];
                    return $methods[$row->metode_pembayaran] ?? $row->metode_pembayaran;
                }),

            Column::make('Margin (%)', 'id')
                ->label(function($row) {
                    if ($row->subtotal > 0) {
                        $margin = ($row->laba_bruto / $row->subtotal) * 100;
                        return number_format($margin, 1) . '%';
                    }
                    return '0%';
                }),
        ];
    }
}
