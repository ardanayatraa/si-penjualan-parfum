<?php

namespace App\Livewire\Table;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\PajakTransaksi;

class PajakTransaksiTable extends DataTableComponent
{
    protected $model = PajakTransaksi::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');

        $this->setTrAttributes(fn($row, $index) => [
            'default' => true,
            'class'   => $index % 2 === 0 ? 'bg-gray-200' : '',
        ]);
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable(),

            Column::make('Nama Pajak', 'nama')
                ->sortable(),

            Column::make('Presentase (%)', 'presentase')
                ->sortable()
                ->format(fn($value) => number_format($value, 2, ',', '.') . '%'),

            Column::make('Aksi', 'id')
                ->label(fn($row) => view('components.link-action', [
                    'id'           => $row->id,
                    'editEvent'    => 'editPajak',
                    'deleteEvent'  => 'deletePajak',
                ]))
                ->html(),
        ];
    }

    public function edit($id)
    {
        $this->dispatch('editPajak', $id);
    }

    public function delete($id)
    {
        $this->dispatch('deletePajak', $id);
    }
}
