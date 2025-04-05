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
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("Id transaksi", "id_transaksi")
                ->sortable(),
            Column::make("Jenis transaksi", "jenis_transaksi")
                ->sortable(),
            Column::make("Persentase pajak", "persentase_pajak")
                ->sortable(),
            Column::make("Nilai pajak", "nilai_pajak")
                ->sortable(),
                Column::make('Aksi', 'id')
                ->label(fn ($row) => view('components.link-action', [
                    'id' => $row->id,
                ]))->html(),
        ];
    }

    public function delete($id)
    {
        $this->dispatch('delete', $id);

    }
    public function edit($id)
    {
        $this->dispatch('edit', $id);
    }
}
