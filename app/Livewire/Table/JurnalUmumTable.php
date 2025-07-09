<?php

namespace App\Livewire\Table;

use App\Models\JurnalUmum;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class JurnalUmumTable extends DataTableComponent
{
    protected $model = JurnalUmum::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id_jurnal');
    }

    public function columns(): array
    {
        return [
              Column::make('ID', 'id_jurnal')
            ->excludeFromColumnSelect(),
            Column::make('Tanggal', 'tanggal')
                ->sortable()
                ->searchable(),

            Column::make('Akun', 'akun.nama_akun')
                ->sortable()
                ->searchable(),

            Column::make('Debit', 'debit')
                ->format(fn($value) => $value ? 'Rp ' . number_format($value, 0, ',', '.') : '-'),

            Column::make('Kredit', 'kredit')
                ->format(fn($value) => $value ? 'Rp ' . number_format($value, 0, ',', '.') : '-'),

            Column::make('Keterangan', 'keterangan')
                ->sortable()
                ->searchable(),

            Column::make('Aksi')
                ->label(function ($row) {
                    return view('components.link-action', [
                        'id' => $row->id_jurnal,
                    ]);
                })
                ->html(),
        ];
    }

    public function edit($id)
    {
        $this->dispatch('editJurnal', $id);
    }

    public function delete($id)
    {
        $this->dispatch('deleteJurnal', $id);
    }
}
