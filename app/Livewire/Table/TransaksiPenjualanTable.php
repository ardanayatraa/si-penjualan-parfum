<?php

namespace App\Livewire\Table;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\TransaksiPenjualan;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
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
                ->sortable(),

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
                 Column::make('Nota', 'id')
                ->label(fn($row) => '<button wire:click="printNota('.$row->id.')" class="px-2 py-1 bg-blue-600 text-white rounded">Cetak Nota</button>')
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

      public function printNota(int $id)
    {
        $trans = TransaksiPenjualan::with(['kasir','barang','pajak'])->findOrFail($id);

        $pdf = Pdf::loadView('exports.nota-pdf', ['transaksi' => $trans])
                  ->setPaper([0, 0, 226.77, 600], 'portrait');

        return response()->streamDownload(
            fn() => print($pdf->stream()),
            "nota-{$trans->id}.pdf"
        );
    }
}
