<?php

namespace App\Livewire\Table;

use App\Models\Hutang;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class HutangTable extends DataTableComponent
{
    protected $model = Hutang::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id_hutang');
    }

    public function columns(): array
    {
        return [

            Column::make('ID', 'id_hutang')
                ->sortable()
                ->searchable(),
            Column::make('Supplier', 'supplier.nama_supplier')
                ->sortable()
                ->searchable(),

            Column::make('Jumlah', 'jumlah')
                ->format(fn($value) => 'Rp ' . number_format($value, 0, ',', '.')),

            Column::make('Tanggal Tempo', 'tgl_tempo')
                ->sortable()
                ->searchable(),

            Column::make('Status', 'status')
                ->sortable(),

            Column::make('Aksi', 'id_hutang')
                ->label(function ($row) {
    logger()->debug('Isi $row:', ['row' => $row]);

    return view('components.link-action', [
        'id' => $row->id_hutang ?? 'null',
    ]);
})
                ->html(),

        ];
    }

    public function edit($id)
    {
        $this->dispatch('editHutang', $id);
    }

    public function delete($id)
    {
        $this->dispatch('deleteHutang', $id);
    }
}
