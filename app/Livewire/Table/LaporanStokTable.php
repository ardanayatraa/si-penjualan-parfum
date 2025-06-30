<?php

namespace App\Livewire\Table;

use App\Models\Barang;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter; // ✅ Tambahkan ini
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;

class LaporanStokTable extends DataTableComponent
{
    protected $model = Barang::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function builder(): Builder
    {
        return Barang::query(); // ✅ Diperlukan untuk filter
    }

    public function filters(): array
    {
        return [
            SelectFilter::make('Nama Barang')
                ->options(
                    Barang::orderBy('nama_barang')->pluck('nama_barang', 'id')->toArray()
                )
                ->filter(function ($builder, $value) {
                    $builder->where('id', $value);
                }),
        ];
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')->sortable(),
            Column::make('Nama Barang', 'nama_barang')->sortable(),
            Column::make('Satuan', 'satuan')->sortable(),
            Column::make('Harga Beli', 'harga_beli')
                ->sortable()
                ->format(fn($v) => 'Rp ' . number_format($v, 0, ',', '.')),
            Column::make('Harga Jual', 'harga_jual')
                ->sortable()
                ->format(fn($v) => 'Rp ' . number_format($v, 0, ',', '.')),
            Column::make('Stok', 'stok')->sortable(),
        ];
    }

    public function bulkActions(): array
    {
        return ['exportPdf' => 'Export PDF'];
    }

    public function exportPdf()
    {
        $selected = $this->getSelected();
        $q = Barang::query()
            ->when($selected, fn($q) => $q->whereIn('id', $selected));

        $data = $q->get();
        $totalStok = $data->sum('stok');
        $totalNilai = $data->sum(fn($item) => $item->stok * $item->harga_beli);

        $pdf = Pdf::loadView('exports.stok-pdf', compact('data', 'totalStok', 'totalNilai'))
                  ->setPaper('a4','landscape');

        $this->clearSelected();

        return response()->streamDownload(fn() => print($pdf->stream()), "laporan-stok.pdf");
    }
}
