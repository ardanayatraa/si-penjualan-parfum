<?php

namespace App\Livewire\Table;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use App\Models\TransaksiPenjualan;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;

class TransaksiPenjualanTable extends DataTableComponent
{
    protected $model = TransaksiPenjualan::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('tanggal_transaksi', 'desc');

        $this->setTrAttributes(fn($row, $index) => [
            'default' => true,
            'class'   => $index % 2 === 0 ? 'bg-gray-50' : 'bg-white',
        ]);
    }

    public function builder(): Builder
    {
        return TransaksiPenjualan::query()
            ->with(['kasir', 'barang', 'pajak']);
    }

    public function filters(): array
    {
        return [
            SelectFilter::make('Status')
                ->options([
                    '' => 'Semua Status',
                    'pending' => 'Pending',
                    'selesai' => 'Selesai',
                    'dibatalkan' => 'Dibatalkan',
                ])
                ->filter(function(Builder $builder, string $value) {
                    if ($value) {
                        $builder->where('status', $value);
                    }
                }),

            SelectFilter::make('Metode Pembayaran')
                ->options([
                    '' => 'Semua Metode',
                    'cash' => 'Tunai',
                    'transfer' => 'Transfer Bank',
                    'debit_card' => 'Kartu Debit',
                    'credit_card' => 'Kartu Kredit',
                    'e_wallet' => 'E-Wallet',
                ])
                ->filter(function(Builder $builder, string $value) {
                    if ($value) {
                        $builder->where('metode_pembayaran', $value);
                    }
                }),

            DateFilter::make('Tanggal Mulai')
                ->filter(function(Builder $builder, string $value) {
                    $builder->whereDate('tanggal_transaksi', '>=', $value);
                }),

            DateFilter::make('Tanggal Akhir')
                ->filter(function(Builder $builder, string $value) {
                    $builder->whereDate('tanggal_transaksi', '<=', $value);
                }),
        ];
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable()
                ->searchable(),

            Column::make('Kode', 'id')
                ->label(fn($row) => 'PNJ-' . str_pad($row->id, 6, '0', STR_PAD_LEFT))
                ->sortable(),

            Column::make('Kasir', 'kasir.username')
                ->sortable()
                ->searchable(),

            Column::make('Barang', 'barang.nama_barang')
                ->sortable()
                ->searchable(),

            Column::make('Tanggal', 'tanggal_transaksi')
                ->sortable()
                ->format(fn($value) => Carbon::parse($value)->format('d-m-Y H:i')),

            Column::make('Jumlah', 'jumlah_terjual') // Updated field name
                ->sortable(),

            Column::make('Subtotal', 'subtotal')
                ->sortable()
                ->format(fn($value) => 'Rp ' . number_format($value, 0, ',', '.')),

            Column::make('Total Harga', 'total_harga')
                ->sortable()
                ->format(fn($value) => 'Rp ' . number_format($value, 0, ',', '.')),

            Column::make('Laba Bruto', 'laba_bruto') // New field
                ->sortable()
                ->format(fn($value) => 'Rp ' . number_format($value, 0, ',', '.')),

            Column::make('Pembayaran', 'metode_pembayaran') // New field
             ,

            Column::make('Status', 'status') // New field
                ,

            Column::make('Aksi', 'id')
                ->label(fn($row) => view('components.link-action-penjualan', [
                    'id'           => $row->id,
                    'row'          => $row,
                    'editEvent'    => 'edit',
                    'deleteEvent'  => 'delete',
                ]))
                ->html(),

            Column::make('Nota', 'id')
                ->label(function($row) {
                    $disabled = $row->status !== 'selesai' ? 'opacity-50 cursor-not-allowed' : '';
                    $onClick = $row->status === 'selesai' ? 'wire:click="printNota('.$row->id.')"' : '';

                    return '<button ' . $onClick . ' class="px-2 py-1 bg-blue-600 text-white rounded text-xs hover:bg-blue-700 ' . $disabled . '"
                            ' . ($row->status !== 'selesai' ? 'disabled title="Nota hanya bisa dicetak untuk transaksi selesai"' : '') . '>
                                <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                </svg>
                                Nota
                            </button>';
                })
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
        $transaksi = TransaksiPenjualan::with(['kasir', 'barang', 'pajak'])->findOrFail($id);

        // Validasi hanya transaksi selesai yang bisa dicetak
        if ($transaksi->status !== 'selesai') {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Nota hanya bisa dicetak untuk transaksi yang sudah selesai!'
            ]);
            return;
        }

        // Hitung pajak amount
        $pajakAmount = 0;
        if ($transaksi->pajak) {
            $pajakAmount = ($transaksi->subtotal * $transaksi->pajak->presentase) / 100;
        }

        $pdf = Pdf::loadView('exports.nota-pdf', [
            'transaksi' => $transaksi,
            'pajakAmount' => $pajakAmount,
        ])->setPaper([0, 0, 226.77, 600], 'portrait'); // 80mm width thermal paper

        return response()->streamDownload(
            fn() => print($pdf->stream()),
            "nota-penjualan-{$transaksi->id}.pdf"
        );
    }

    public function bulkActions(): array
    {
        return [
            'exportSelected' => 'Export Selected PDF',
            'markAsCompleted' => 'Tandai Selesai',
            'markAsPending' => 'Tandai Pending',
        ];
    }

    public function exportSelected()
    {
        $selected = $this->getSelected();

        if (empty($selected)) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Pilih transaksi yang akan diekspor!'
            ]);
            return;
        }

        $transaksis = TransaksiPenjualan::with(['kasir', 'barang', 'pajak'])
            ->whereIn('id', $selected)
            ->get();

        $pdf = Pdf::loadView('exports.transaksi-penjualan-bulk-pdf', [
            'transaksis' => $transaksis,
        ])->setPaper('a4', 'landscape');

        $this->clearSelected();

        return response()->streamDownload(
            fn() => print($pdf->stream()),
            "transaksi-penjualan-bulk-" . now()->format('Y-m-d') . ".pdf"
        );
    }

    public function markAsCompleted()
    {
        $selected = $this->getSelected();

        if (empty($selected)) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Pilih transaksi yang akan diselesaikan!'
            ]);
            return;
        }

        $updated = TransaksiPenjualan::whereIn('id', $selected)
            ->where('status', 'pending')
            ->update(['status' => 'selesai']);

        $this->clearSelected();

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => "{$updated} transaksi berhasil ditandai selesai!"
        ]);
    }

    public function markAsPending()
    {
        $selected = $this->getSelected();

        if (empty($selected)) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Pilih transaksi yang akan dijadikan pending!'
            ]);
            return;
        }

        $updated = TransaksiPenjualan::whereIn('id', $selected)
            ->where('status', 'selesai')
            ->update(['status' => 'pending']);

        $this->clearSelected();

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => "{$updated} transaksi berhasil ditandai pending!"
        ]);
    }
}
