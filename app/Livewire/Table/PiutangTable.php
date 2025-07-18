<?php

namespace App\Livewire\Table;

use App\Models\Piutang;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PiutangTable extends DataTableComponent
{
    protected $model = Piutang::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id_piutang');
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id_piutang')
                ->sortable()
                ->searchable()
                ->html()
                ->format(fn($value) => '<strong>' . $value . '</strong>'),

            Column::make('Nama Barang', 'penjualan.barang.nama_barang')
                ->sortable(),
            Column::make('Harga Barang', 'penjualan.barang.harga_jual')
                ->sortable(),
            Column::make('Tanggal Transaksi', 'penjualan.tanggal_transaksi')
                ->sortable(),

            Column::make('Nama Pelanggan', 'nama_pelanggan')
                ->sortable()
                ->searchable(),

            Column::make('No Telepon', 'no_telp')
                ->sortable()
                ->searchable(),

            Column::make('Jumlah', 'jumlah')
                ->format(fn($value) => 'Rp ' . number_format($value, 0, ',', '.')),

            Column::make('Status', 'status')
                ->sortable(),

            Column::make('Aksi', 'id_piutang')
                ->label(fn ($row) => view('components.link-action', [
                    'id' => $row->id_piutang,
                ]))
                ->html(),
        ];
    }

    public function edit($id)
    {
        $this->dispatch('editPiutang', $id);
    }

    public function delete($id)
    {
        $this->dispatch('deletePiutang', $id);
    }
}
