<?php

namespace App\Livewire\Table;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Akun;

class AkunTable extends DataTableComponent
{
    protected $model = Akun::class;

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

            Column::make('Kode Akun', 'kode_akun')
                ->sortable()
                ->searchable(),

            Column::make('Nama Akun', 'nama_akun')
                ->sortable()
                ->searchable(),

            Column::make('Tipe Akun', 'tipe_akun')
                ->sortable()
                ->searchable(),

            Column::make('Parent', 'parent.nama_akun')
                ->sortable()
                ->searchable(),

            Column::make('Aksi', 'id')
                ->label(fn($row) => view('components.link-action', [
                    'id'          => $row->id,
                    'editEvent'   => 'edit',
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
}
