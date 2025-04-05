<?php

namespace App\Livewire\Table;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Barang;

class BarangTable extends DataTableComponent
{
    protected $model = Barang::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');

        $this->setTrAttributes(function($row, $index) {
            if ($index % 2 === 0) {
              return [
                'default' => true,
                'class' => 'bg-gray-200',
              ];
            }

            return ['default' => true];
        });
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("Nama barang", "nama_barang")
                ->sortable(),
            Column::make("Harga beli", "harga_beli")
                ->sortable(),
            Column::make("Harga jual", "harga_jual")
                ->sortable(),
            Column::make("Jumlah retur", "jumlah_retur")
                ->sortable(),
            Column::make("Jumlah terjual", "jumlah_terjual")
                ->sortable(),
            Column::make("Jumlah stok", "jumlah_stok")
                ->sortable(),
            Column::make("Jumlah nilai stok", "jumlah_nilai_stok")
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
