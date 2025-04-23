<?php

namespace App\Livewire\Table;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\TransaksiPenjualan;

class TransaksiPenjualanTable extends DataTableComponent
{
    protected $model = TransaksiPenjualan::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("Id kasir", "id_kasir")
                ->sortable(),
            Column::make("Id barang", "id_barang")
                ->sortable(),
            Column::make("Tanggal transaksi", "tanggal_transaksi")
                ->sortable(),
            Column::make("Jumlah", "jumlah")
                ->sortable(),
            Column::make("Harga Jual", "harga_jual")
                ->sortable(),
            Column::make("Total harga", "total_harga")
                ->sortable(),
            Column::make("Total nilai transaksi", "total_nilai_transaksi")
                ->sortable(),


            Column::make("Laba bruto", "laba_bruto")
                ->sortable(),
            Column::make("Laba bersih", "laba_bersih")
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
