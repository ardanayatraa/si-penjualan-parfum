<?php

namespace App\Livewire\Table;

use App\Models\Pengeluaran;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PengeluaranTable extends DataTableComponent
{
    protected $model = Pengeluaran::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id_pengeluaran');
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id_pengeluaran')
                ->sortable()
                ->searchable()
                ->html()
                ->format(fn($value) => '<strong>' . $value . '</strong>'),
            Column::make('Tanggal', 'tanggal')
                ->sortable()
                ->searchable(),

            Column::make('Jenis Pengeluaran', 'jenis_pengeluaran')
                ->sortable()
                ->searchable(),

            Column::make('Jumlah', 'jumlah')
                ->sortable()
                ->format(fn($value) => 'Rp ' . number_format($value, 0, ',', '.')),

            Column::make('Akun', 'akun.nama_akun')
                ->sortable()
                ->searchable(),

            Column::make('Pencatat', 'user.username')
                ->sortable()
                ->searchable(),

            Column::make('Keterangan', 'keterangan')
                ->sortable()
                ->searchable(),

            Column::make('Aksi', 'id_pengeluaran')
                ->label(fn ($row) => view('components.link-action', [
                    'id' => $row->id_pengeluaran,
                ]))
                ->html(),
        ];
    }

    public function edit($id)
    {
        $this->dispatch('editPengeluaran', $id);
    }

    public function delete($id)
    {
        $this->dispatch('deletePengeluaran', $id);
    }
}
