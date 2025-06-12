<?php

namespace App\Livewire\Table;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\ReturnBarang;

class ReturnBarangTable extends DataTableComponent
{
    protected $model = ReturnBarang::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');

        $this->setTrAttributes(function($row, $index) {
            if ($index % 2 === 0) {
                return [
                    'default' => true,
                    'class'   => 'bg-gray-200',
                ];
            }
            return ['default' => true];
        });
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable(),

            Column::make('Barang', 'barang.nama_barang')
                ->sortable()
,
            Column::make('Supplier', 'supplier.nama_supplier')
                ->sortable()
               ,

            Column::make('Jumlah', 'jumlah')
                ->sortable(),

            Column::make('Alasan', 'alasan')
                ->sortable()
                ,

            Column::make('Tanggal Return', 'tanggal_return')
                ->sortable()
                ->format(fn($value) => \Carbon\Carbon::parse($value)->format('d-m-Y')),

            Column::make('Aksi', 'id')
                ->label(fn($row) => view('components.link-action', [
                    'id'           => $row->id,
                    'editEvent'    => 'editReturn',
                    'deleteEvent'  => 'deleteReturn',
                ]))
                ->html(),
        ];
    }

    public function edit($id)
    {
        $this->dispatch('editReturn', $id);
    }

    public function delete($id)
    {
        $this->dispatch('deleteReturn', $id);
    }
}
