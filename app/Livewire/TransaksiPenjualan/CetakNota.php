<?php

namespace App\Livewire\TransaksiPenjualan;

use Livewire\Component;
use App\Models\TransaksiPenjualan;
use Barryvdh\DomPDF\Facade\Pdf;

class CetakNota extends Component
{
    public $open = false;
    public $transaksi;

    protected $listeners = ['printNota' => 'show'];

    /**
     * Muat data transaksi dan buka modal
     */
    public function show($id)
    {
        $this->transaksi = TransaksiPenjualan::with(['kasir','barang','pajak'])
            ->findOrFail($id);
        $this->open = true;
    }

    /**
     * Generate PDF nota dan stream download
     */
    public function print()
    {
        $pdf = Pdf::loadView('exports.nota-pdf', [
            'transaksi' => $this->transaksi,
        ])->setPaper([0, 0, 226.77, 600], 'portrait');

        return response()->streamDownload(
            fn() => print($pdf->stream()),
            "nota-{$this->transaksi->id}.pdf"
        );
    }

    public function render()
    {
        return view('livewire.transaksi-penjualan.cetak-nota');
    }
}
