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
        $this->setPrimaryKey('id_akun');

        $this->setTrAttributes(fn ($row, $index) => [
            'default' => true,
            'class'   => $index % 2 === 0 ? 'bg-gray-200' : '',
        ]);
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id_akun')
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

            Column::make('Kategori Akun', 'kategori_akun')
                ->sortable()
                ->searchable(),

            Column::make('Saldo Awal', 'saldo_awal')
                ->sortable()
                ->format(fn($value) => 'Rp ' . number_format($value, 0, ',', '.')),

            Column::make('Aksi', 'id_akun')
                ->label(fn ($row) => view('components.link-action-akun', [
                    'id'          => $row->id_akun,
                    'editEvent'   => 'edit',
                    'deleteEvent' => 'delete',
                ]))
                ->html(),
        ];
    }

    public function edit($id)
    {
        $this->dispatch('editAkun', $id);
    }

    public function delete($id)
    {
        $this->dispatch('deleteAkun', $id);
    }
}
