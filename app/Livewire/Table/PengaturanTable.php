<?php

namespace App\Livewire\Table;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Pengaturan;

class PengaturanTable extends DataTableComponent
{
    protected $model = Pengaturan::class;

    public function configure(): void
    {
        $this->setPrimaryKey('nama_pengaturan');
    }

    public function columns(): array
    {
        return [
            Column::make("Nama Pengaturan", "nama_pengaturan")
                ->sortable()
                ->searchable(),

            Column::make("Nilai", "nilai_pengaturan")
                ->sortable()
                ->format(fn($value) => number_format($value, 0, ',', '.')),

            Column::make("Keterangan", "keterangan")
                ->sortable()
                ->searchable(),

            Column::make('Aksi', 'nama_pengaturan')
                ->label(fn ($row) => view('components.link-action', [
                    'id'       => $row->nama_pengaturan,
                    'editEvent'   => 'editPengaturan',
                    'deleteEvent' => 'deletePengaturan',
                ]))
                ->html(),
        ];
    }

    public function editPengaturan($nama_pengaturan)
    {
        $this->dispatch('editPengaturan', $nama_pengaturan);
    }

    public function deletePengaturan($nama_pengaturan)
    {
        $this->dispatch('deletePengaturan', $nama_pengaturan);
    }
}
