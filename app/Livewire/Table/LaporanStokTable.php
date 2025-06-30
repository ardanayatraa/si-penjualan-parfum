<?php

namespace App\Livewire\Table;

use App\Models\Barang;
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
    }

    public function builder(): Builder
    {
        return Barang::query()
            // hitung qty dan nilai dari pembelian
            ->withSum('transaksiPembelian as qty_pembelian', 'jumlah_pembelian')
            ->withSum('transaksiPembelian as nilai_pembelian', 'total')
            // hitung qty dan nilai dari penjualan
            ->withSum('transaksiPenjualan as qty_penjualan', 'jumlah_penjualan')
            ->withSum('transaksiPenjualan as nilai_penjualan', 'total_harga');
    }

    public function filters(): array
    {
        return [
            SelectFilter::make('Nama Barang')
                ->options(
                    Barang::orderBy('nama_barang')->pluck('nama_barang', 'id')->toArray()
                )
                ->filter(fn($builder, $value) => $builder->where('id', $value)),
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
                ->format(fn($v) => 'Rp '.number_format($v,0,',','.')),
            Column::make('Harga Jual', 'harga_jual')
                ->sortable()
                ->format(fn($v) => 'Rp '.number_format($v,0,',','.') ),

            Column::make('Stok', 'stok')->sortable(),

            Column::make('Jumlah Beli', 'qty_pembelian')
                ->label(fn($row) => $row->qty_pembelian ?? 0),

            Column::make('Total Beli', 'nilai_pembelian')
                ->label(fn($row) => 'Rp '.number_format($row->nilai_pembelian ?? 0,0,',','.') ),

            Column::make('Jumlah Jual', 'qty_penjualan')
                ->label(fn($row) => $row->qty_penjualan ?? 0),

            Column::make('Total Jual', 'nilai_penjualan')
                ->label(fn($row) => 'Rp '.number_format($row->nilai_penjualan ?? 0,0,',','.') ),
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
            ->withSum('transaksiPembelian as qty_pembelian', 'jumlah_pembelian')
            ->withSum('transaksiPembelian as nilai_pembelian', 'total')
            ->withSum('transaksiPenjualan as qty_penjualan', 'jumlah_penjualan')
            ->withSum('transaksiPenjualan as nilai_penjualan', 'total_harga')
            ->when($selected, fn($q) => $q->whereIn('id', $selected));

        $data             = $q->get();
        $totalStok        = $data->sum('stok');
        $totalNilaiStok   = $data->sum(fn($i) => $i->stok * $i->harga_beli);
        $totalNilaiBeli   = $data->sum('nilai_pembelian');
        $totalNilaiJual   = $data->sum('nilai_penjualan');

        $pdf = Pdf::loadView('exports.stok-pdf', compact(
            'data',
            'totalStok',
            'totalNilaiStok',
            'totalNilaiBeli',
            'totalNilaiJual'
        ))->setPaper('a4', 'landscape');

        $this->clearSelected();

        return response()->streamDownload(fn() => print($pdf->stream()), "laporan-stok.pdf");
    }
}
