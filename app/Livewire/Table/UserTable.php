<?php

namespace App\Livewire\Table;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\User;

class UserTable extends DataTableComponent
{
    protected $model = User::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("Level", "level")
                ->sortable(),
            Column::make("Username", "username")
                ->sortable(),
            Column::make("No telp", "no_telp")
                ->sortable(),
            Column::make("Alamat", "alamat")
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
