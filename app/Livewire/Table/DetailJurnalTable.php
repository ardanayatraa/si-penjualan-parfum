<?php

namespace App\Livewire\Table;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Illuminate\Database\Eloquent\Builder;
use App\Models\DetailJurnal;

class DetailJurnalTable extends DataTableComponent
{
    protected $model = DetailJurnal::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setTrAttributes(fn($row, $index) => [
            'default' => true,
            'class'   => $index % 2 === 0 ? 'bg-gray-200' : '',
        ]);
    }

    public function builder(): Builder
    {
        return DetailJurnal::query()
            ->with(['jurnalUmum', 'akun']);
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable(),

            Column::make('No. Bukti', 'jurnalUmum.no_bukti')
                ->sortable()
                ->searchable(),

            Column::make('Keterangan', 'jurnalUmum.keterangan')
                ->sortable()
                ->searchable(),

            Column::make('Akun', 'akun.nama_akun')
                ->sortable()
                ->searchable(),

            Column::make('Debit', 'debit')
                ->sortable()
                ->format(fn($value) => number_format($value, 0, ',', '.')),

            Column::make('Kredit', 'kredit')
                ->sortable()
                ->format(fn($value) => number_format($value, 0, ',', '.')),

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
