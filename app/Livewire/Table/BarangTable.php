<?php

namespace App\Livewire\Table;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use App\Models\Barang;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Builder;

class BarangTable extends DataTableComponent
{
    protected $model = Barang::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('nama_barang', 'asc');

        $this->setTrAttributes(fn($row, $index) => [
            'default' => true,
            'class'   => $index % 2 === 0 ? 'bg-gray-50' : 'bg-white',
        ]);
    }

    public function builder(): Builder
    {
        return Barang::query()
            ->with(['supplier']);
    }

    public function filters(): array
    {
        return [
            SelectFilter::make('Supplier')
                ->options([
                    '' => 'Semua Supplier',
                    ...Supplier::orderBy('nama_supplier')->pluck('nama_supplier', 'id_supplier')->toArray()
                ])
                ->filter(function (Builder $builder, string $value) {
                    if ($value) {
                        // Use table prefix to avoid ambiguous column
                        $builder->where('barang.id_supplier', $value);
                    }
                }),

            SelectFilter::make('Status Stok')
                ->options([
                    '' => 'Semua Status',
                    'in_stock' => 'Stok Tersedia',
                    'low_stock' => 'Stok Menipis',
                    'out_of_stock' => 'Stok Habis',
                ])
                ->filter(function (Builder $builder, string $value) {
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
        ];
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable(),

            Column::make('Supplier', 'supplier.nama_supplier')
                ->sortable()
                ->searchable(),

            Column::make('Nama Barang', 'nama_barang')
                ->sortable()
                ->searchable(),

            Column::make('Satuan', 'satuan')
                ->sortable(),

            Column::make('Harga Beli', 'harga_beli')
                ->sortable()
                ->format(fn($value) => 'Rp ' . number_format($value, 0, ',', '.')),

            Column::make('Harga Jual', 'harga_jual')
                ->sortable()
                ->format(fn($value) => 'Rp ' . number_format($value, 0, ',', '.')),

            Column::make('Margin', 'id')
                ->label(function($row) {
                    if ($row->harga_beli > 0) {
                        $margin = (($row->harga_jual - $row->harga_beli) / $row->harga_beli) * 100;
                        $color = $margin > 0 ? 'text-green-600' : 'text-red-600';
                        return '<span class="' . $color . ' font-medium">' . number_format($margin, 1) . '%</span>';
                    }
                    return '<span class="text-gray-500">0%</span>';
                })
                ->html(),

            Column::make('Stok', 'stok')
                ->sortable()
                ->label(function($row) {
                    $badgeClass = match(true) {
                        $row->stok == 0 => 'bg-red-100 text-red-800',
                        $row->stok <= 10 => 'bg-yellow-100 text-yellow-800',
                        default => 'bg-green-100 text-green-800'
                    };

                    $statusText = match(true) {
                        $row->stok == 0 => 'Habis',
                        $row->stok <= 10 => 'Menipis',
                        default => 'Aman'
                    };

                    return '<div class="text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $badgeClass . '">
                                    ' . $row->stok . '
                                </span>
                                <div class="text-xs text-gray-500 mt-1">' . $statusText . '</div>
                            </div>';
                })
                ->html(),

            Column::make('Nilai Stok', 'id')
                ->label(function($row) {
                    $nilaiStok = $row->stok * $row->harga_beli;
                    return 'Rp ' . number_format($nilaiStok, 0, ',', '.');
                })
                ->sortable(),

            Column::make('Aksi', 'id')
                ->label(fn($row) => view('components.link-action-barang', [
                    'id'        => $row->id,
                    'editEvent' => 'edit',
                    'deleteEvent' => 'delete',
                ]))
                ->html(),
        ];
    }

    public function edit($id)
    {
        $this->dispatch('edit', $id);
    }

    public function delete($id)
    {
        $this->dispatch('delete', $id);
    }

    public function bulkActions(): array
    {
        return [
            'exportPdf' => 'Export PDF',
            'updateStockStatus' => 'Update Status Stok',
        ];
    }

    public function exportPdf()
    {
        $selected = $this->getSelected();

        if (empty($selected)) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Pilih barang yang akan diekspor!'
            ]);
            return;
        }

        $barangs = Barang::with('supplier')
            ->whereIn('id', $selected)
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.barang-pdf', [
            'barangs' => $barangs,
        ])->setPaper('a4', 'landscape');

        $this->clearSelected();

        return response()->streamDownload(
            fn() => print($pdf->stream()),
            "data-barang-" . now()->format('Y-m-d') . ".pdf"
        );
    }

    public function updateStockStatus()
    {
        $selected = $this->getSelected();

        if (empty($selected)) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Pilih barang yang akan diupdate!'
            ]);
            return;
        }

        $lowStockCount = Barang::whereIn('id', $selected)
            ->where('stok', '<=', 10)
            ->where('stok', '>', 0)
            ->count();

        $outOfStockCount = Barang::whereIn('id', $selected)
            ->where('stok', 0)
            ->count();

        $this->clearSelected();

        $message = "Status stok diperbarui: {$lowStockCount} barang stok menipis, {$outOfStockCount} barang stok habis";

        $this->dispatch('show-toast', [
            'type' => 'info',
            'message' => $message
        ]);
    }
}
