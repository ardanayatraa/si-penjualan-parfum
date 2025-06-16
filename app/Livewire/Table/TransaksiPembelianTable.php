<?php

namespace App\Livewire\Table;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\TransaksiPembelian;
use Carbon\Carbon;

class TransaksiPembelianTable extends DataTableComponent
{
    protected $model = TransaksiPembelian::class;

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

            Column::make('Barang', 'barang.nama_barang')
                ->sortable()
                ->searchable(),

            Column::make('Supplier', 'barang.supplier.nama_supplier')
                ->sortable()
                ->searchable(),

            Column::make('Tanggal Transaksi', 'tanggal_transaksi')
                ->sortable()
                ->format(fn($value) => Carbon::parse($value)->format('d-m-Y')),

            Column::make('Jumlah Pembelian', 'jumlah_pembelian')
                ->sortable(),

            Column::make('Total', 'total')
                ->sortable()
                ->format(fn($value) => number_format($value, 2, ',', '.')),

            Column::make('Aksi', 'id')
                ->label(fn($row) => view('components.link-action', [
                    'id'           => $row->id,
                    'editEvent'    => 'editTransaksi',
                    'deleteEvent'  => 'deleteTransaksi',
                ]))
                ->html(),
        ];
    }

    public function edit($id)
    {
        $this->dispatch('editTransaksi', $id);
    }

    public function delete($id)
    {
        $this->dispatch('deleteTransaksi', $id);
    }
}
