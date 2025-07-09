<?php

namespace App\Livewire\Table;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use App\Models\Barang;
use App\Models\Supplier;

class BarangTable extends DataTableComponent
{
    protected $model = Barang::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');

        $this->setTrAttributes(fn($row, $index) => [
            'default' => true,
            'class'   => $index % 2 === 0 ? 'bg-gray-200' : '',
        ]);
    }

    public function filters(): array
    {
        return [
            SelectFilter::make('Supplier')
                ->options(
                    Supplier::orderBy('nama_supplier')->pluck('nama_supplier', 'id_supplier')->toArray()
                )
                ->filter(function ($builder, string $value) {
                    $builder->where('id_supplier', $value);
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
                ->format(fn($value) => number_format($value, 0, ',', '.')),

            Column::make('Harga Jual', 'harga_jual')
                ->sortable()
                ->format(fn($value) => number_format($value, 0, ',', '.')),

            Column::make('Stok', 'stok')
                ->sortable(),

            Column::make('Aksi', 'id')
                ->label(fn($row) => view('components.link-action-barang', [
                    'id'        => $row->id,
                    'editEvent' => 'edit',
                ]))
                ->html(),
        ];
    }

    public function edit($id)
    {
        $this->dispatch('edit', $id);
    }
}
