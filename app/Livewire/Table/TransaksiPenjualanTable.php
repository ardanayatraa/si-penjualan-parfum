<?php

namespace App\Livewire\Table;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\TransaksiPenjualan;
use Carbon\Carbon;

class TransaksiPenjualanTable extends DataTableComponent
{
    protected $model = TransaksiPenjualan::class;

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

            Column::make('Kasir', 'kasir.username')
                ->sortable()
                ->format(fn($value, $row) => $row->kasir->name ?? '-'),

            Column::make('Barang', 'barang.nama_barang')
                ->sortable()
              ,

            Column::make('Pajak', 'pajak.nama')
                ->sortable()
               ,
            Column::make('Jumlah', 'jumlah_penjualan')
                ->sortable()
            ,
            Column::make('Tanggal', 'tanggal_transaksi')
                ->sortable()
                ->format(fn($value) => Carbon::parse($value)->format('d-m-Y')),

            Column::make('Subtotal', 'subtotal')
                ->sortable()
                ->format(fn($value) => number_format($value, 2, ',', '.')),

            Column::make('Harga Pokok', 'harga_pokok')
                ->sortable()
                ->format(fn($value) => number_format($value, 2, ',', '.')),

            Column::make('Laba Bruto', 'laba_bruto')
                ->sortable()
                ->format(fn($value) => number_format($value, 2, ',', '.')),

            Column::make('Total Harga', 'total_harga')
                ->sortable()
                ->format(fn($value) => number_format($value, 2, ',', '.')),

            Column::make('Aksi', 'id')
                ->label(fn($row) => view('components.link-action', [
                    'id'           => $row->id,
                    'editEvent'    => 'edit',
                    'deleteEvent'  => 'delete',
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
