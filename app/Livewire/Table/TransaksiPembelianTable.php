<?php

namespace App\Livewire\Table;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\TransaksiPembelian;

class TransaksiPembelianTable extends DataTableComponent
{
    protected $model = TransaksiPembelian::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("Id barang", "id_barang")
                ->sortable(),
            Column::make("Id supplier", "id_supplier")
                ->sortable(),
            Column::make("Tanggal", "tanggal")
                ->sortable(),
            Column::make("Nama barang", "nama_barang")
                ->sortable(),
            Column::make("Harga beli", "harga_beli")
                ->sortable(),
            Column::make("Jumlah", "jumlah")
                ->sortable(),
            Column::make("Total harga beli", "total_harga_beli")
                ->sortable(),
            Column::make("Total nilai transaksi", "total_nilai_transaksi")
                ->sortable(),
            Column::make("Keterangan", "keterangan")
                ->sortable(),

                Column::make('Aksi', 'id')
                ->label(fn ($row) => view('components.link-action', [
                    'id' => $row->id,
                ]))->html(),

        ];
    }

    public function delete($id)
    {
        $this->dispatch('delete', $id);

    }
    public function edit($id)
    {
        $this->dispatch('edit', $id);
    }
}
