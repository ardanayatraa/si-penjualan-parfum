<?php

namespace App\Livewire\Table;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Supplier;

class SupplierTable extends DataTableComponent
{
    protected $model = Supplier::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("Id supplier", "id_supplier")
                ->sortable(),
            Column::make("Nama supplier", "nama_supplier")
                ->sortable(),
            Column::make("Alamat", "alamat")
                ->sortable(),
            Column::make("No telp", "no_telp")
                ->sortable(),
                Column::make('Aksi', 'id_supplier')
                ->label(fn ($row) => view('components.link-action', [
                    'id' => $row->id_supplier,
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
