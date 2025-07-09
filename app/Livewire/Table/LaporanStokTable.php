<?php

namespace App\Livewire\Table;

use App\Models\Barang;
use App\Models\Supplier;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;

class LaporanStokTable extends DataTableComponent
{
    protected $model = Barang::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('nama_barang', 'asc');
    }

    public function builder(): Builder
    {
        return Barang::query()
            ->with(['supplier'])
            // Hitung qty dan nilai dari pembelian (hanya yang selesai)
            ->withSum(['transaksiPembelians as qty_pembelian' => function($query) {
                $query->where('status', 'selesai');
            }], 'jumlah_pembelian')
            ->withSum(['transaksiPembelians as nilai_pembelian' => function($query) {
                $query->where('status', 'selesai');
            }], 'total')
            // Hitung qty dan nilai dari penjualan (hanya yang selesai)
            ->withSum(['transaksiPenjualans as qty_penjualan' => function($query) {
                $query->where('status', 'selesai');
            }], 'jumlah_terjual') // Updated field name
            ->withSum(['transaksiPenjualans as nilai_penjualan' => function($query) {
                $query->where('status', 'selesai');
            }], 'total_harga');
    }

    public function filters(): array
    {
        return [
            SelectFilter::make('Supplier')
                ->options([
                    '' => 'Semua Supplier',
                    ...Supplier::orderBy('nama_supplier')->pluck('nama_supplier', 'id_supplier')->toArray()
                ])
                ->filter(function(Builder $builder, string $value) {
                    if ($value) {
                        $builder->where('id_supplier', $value);
                    }
                }),

            SelectFilter::make('Status Stok')
                ->options([
                    '' => 'Semua Status',
                    'in_stock' => 'Stok Tersedia',
                    'low_stock' => 'Stok Menipis',
                    'out_of_stock' => 'Stok Habis',
                ])
                ->filter(function(Builder $builder, string $value) {
                    switch ($value) {
                        case 'in_stock':
                            $builder->where('stok', '>', 10);
                            break;
                        case 'low_stock':
                            $builder->where('stok', '>', 0)->where('stok', '<=', 10);
                            break;
                        case 'out_of_stock':
                            $builder->where('stok', 0);
                            break;
                    }
                }),

            SelectFilter::make('Nama Barang')
                ->options([
                    '' => 'Semua Barang',
                    ...Barang::orderBy('nama_barang')->pluck('nama_barang', 'id')->toArray()
                ])
                ->filter(function(Builder $builder, string $value) {
                    if ($value) {
                        $builder->where('id', $value);
                    }
                }),
        ];
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable(),

            Column::make('Nama Barang', 'nama_barang')
                ->sortable()
                ->searchable(),

            Column::make('Supplier', 'supplier.nama_supplier') // Updated relationship
                ->sortable()
                ->searchable(),

            Column::make('Harga Beli', 'harga_beli')
                ->sortable()
                ->format(fn($value) => 'Rp ' . number_format($value, 0, ',', '.')),

            Column::make('Harga Jual', 'harga_jual')
                ->sortable()
                ->format(fn($value) => 'Rp ' . number_format($value, 0, ',', '.')),

            Column::make('Stok Saat Ini', 'stok')
                ->sortable()
                ->label(function($row) {
                    $badgeClass = match(true) {
                        $row->stok == 0 => 'bg-red-100 text-red-800',
                        $row->stok <= 10 => 'bg-yellow-100 text-yellow-800',
                        default => 'bg-green-100 text-green-800'
                    };

                    return '<span class="px-2 py-1 text-xs rounded-full ' . $badgeClass . '">' . $row->stok . '</span>';
                })
                ->html(),

            Column::make('Nilai Stok', 'id')
                ->label(function($row) {
                    $nilaiStok = $row->stok * $row->harga_beli;
                    return 'Rp ' . number_format($nilaiStok, 0, ',', '.');
                })
                ->sortable(),

            Column::make('Total Pembelian', 'qty_pembelian')
                ->label(fn($row) => number_format($row->qty_pembelian ?? 0, 0, ',', '.'))
                ->sortable(),

            Column::make('Nilai Pembelian', 'nilai_pembelian')
                ->label(fn($row) => 'Rp ' . number_format($row->nilai_pembelian ?? 0, 0, ',', '.'))
                ->sortable(),

            Column::make('Total Penjualan', 'qty_penjualan')
                ->label(fn($row) => number_format($row->qty_penjualan ?? 0, 0, ',', '.'))
                ->sortable(),

            Column::make('Nilai Penjualan', 'nilai_penjualan')
                ->label(fn($row) => 'Rp ' . number_format($row->nilai_penjualan ?? 0, 0, ',', '.'))
                ->sortable(),

            Column::make('Turnover', 'id')
                ->label(function($row) {
                    $totalPembelian = $row->qty_pembelian ?? 0;
                    $totalPenjualan = $row->qty_penjualan ?? 0;

                    if ($totalPembelian > 0) {
                        $turnover = ($totalPenjualan / $totalPembelian) * 100;
                        return number_format($turnover, 1) . '%';
                    }
                    return '0%';
                }),

            Column::make('Margin', 'id')
                ->label(function($row) {
                    if ($row->harga_beli > 0) {
                        $margin = (($row->harga_jual - $row->harga_beli) / $row->harga_beli) * 100;
                        $color = $margin > 0 ? 'text-green-600' : 'text-red-600';
                        return '<span class="' . $color . '">' . number_format($margin, 1) . '%</span>';
                    }
                    return '0%';
                })
                ->html(),
        ];
    }

    public function bulkActions(): array
    {
        return [
            'exportPdf' => 'Export PDF',
            'exportAllPdf' => 'Export All PDF',
            'exportLowStock' => 'Export Stok Menipis',
        ];
    }

    public function exportPdf()
    {
        $selected = $this->getSelected();

        if (empty($selected)) {
            $this->addError('export', 'Pilih barang yang akan diekspor terlebih dahulu.');
            return;
        }

        $query = $this->buildQueryForExport()
            ->whereIn('id', $selected);

        return $this->generatePdfReport($query, 'selected');
    }

    public function exportAllPdf()
    {
        $query = $this->buildQueryForExport();
        return $this->generatePdfReport($query, 'all');
    }

    public function exportLowStock()
    {
        $query = $this->buildQueryForExport()
            ->where('stok', '<=', 10);

        return $this->generatePdfReport($query, 'low-stock');
    }

    private function buildQueryForExport()
    {
        return Barang::query()
            ->with(['supplier'])
            ->withSum(['transaksiPembelians as qty_pembelian' => function($query) {
                $query->where('status', 'selesai');
            }], 'jumlah_pembelian')
            ->withSum(['transaksiPembelians as nilai_pembelian' => function($query) {
                $query->where('status', 'selesai');
            }], 'total')
            ->withSum(['transaksiPenjualans as qty_penjualan' => function($query) {
                $query->where('status', 'selesai');
            }], 'jumlah_terjual')
            ->withSum(['transaksiPenjualans as nilai_penjualan' => function($query) {
                $query->where('status', 'selesai');
            }], 'total_harga')
            ->orderBy('nama_barang');
    }

    private function generatePdfReport($query, $type = 'selected')
    {
        $data = $query->get();

        if ($data->isEmpty()) {
            $this->addError('export', 'Tidak ada data untuk diekspor.');
            return;
        }

        // Calculate totals and statistics
        $totalStok = $data->sum('stok');
        $totalNilaiStok = $data->sum(fn($item) => $item->stok * $item->harga_beli);
        $totalNilaiBeli = $data->sum('nilai_pembelian');
        $totalNilaiJual = $data->sum('nilai_penjualan');
        $totalQtyBeli = $data->sum('qty_pembelian');
        $totalQtyJual = $data->sum('qty_penjualan');

        // Stock status analysis
        $stokHabis = $data->where('stok', 0)->count();
        $stokMenupis = $data->where('stok', '>', 0)->where('stok', '<=', 10)->count();
        $stokAman = $data->where('stok', '>', 10)->count();

        // Supplier analysis
        $supplierStats = $data->groupBy('supplier.nama_supplier')->map(function($group) {
            return [
                'count' => $group->count(),
                'total_stok' => $group->sum('stok'),
                'total_nilai' => $group->sum(fn($item) => $item->stok * $item->harga_beli),
            ];
        });

        // Top products by value
        $topProductsByValue = $data->sortByDesc(fn($item) => $item->stok * $item->harga_beli)->take(10);

        // Calculate overall turnover
        $overallTurnover = $totalQtyBeli > 0 ? ($totalQtyJual / $totalQtyBeli) * 100 : 0;

        $pdf = Pdf::loadView('exports.stok-pdf', [
            'data' => $data,
            'exportType' => $type,
            'statistics' => [
                'totalItems' => $data->count(),
                'totalStok' => $totalStok,
                'totalNilaiStok' => $totalNilaiStok,
                'totalNilaiBeli' => $totalNilaiBeli,
                'totalNilaiJual' => $totalNilaiJual,
                'totalQtyBeli' => $totalQtyBeli,
                'totalQtyJual' => $totalQtyJual,
                'overallTurnover' => $overallTurnover,
                'rataRataNilaiPerItem' => $data->count() > 0 ? $totalNilaiStok / $data->count() : 0,
            ],
            'stockStatus' => [
                'habis' => $stokHabis,
                'menipis' => $stokMenupis,
                'aman' => $stokAman,
            ],
            'supplierStats' => $supplierStats,
            'topProductsByValue' => $topProductsByValue,
        ])->setPaper('a4', 'landscape');

        $this->clearSelected();

        $filename = "laporan-stok-{$type}-" . now()->format('Y-m-d') . ".pdf";

        return response()->streamDownload(
            fn() => print($pdf->stream()),
            $filename
        );
    }
}
