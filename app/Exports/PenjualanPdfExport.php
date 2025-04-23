<?php

namespace App\Exports;

use App\Models\TransaksiPenjualan;
use Barryvdh\DomPDF\Facade\Pdf;

class PenjualanPdfExport
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function download()
    {
        $query = TransaksiPenjualan::with(['kasir', 'barang', 'pajak']);

        if ($this->startDate) {
            $query->whereDate('tanggal_transaksi', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('tanggal_transaksi', '<=', $this->endDate);
        }

        $penjualan = $query->get()->map(function ($item) {
            return collect($item)->map(function ($value) {
                return is_string($value) ? mb_convert_encoding($value, 'UTF-8', 'UTF-8') : $value;
            });
        });




        $pdf = Pdf::loadView('exports.penjualan-pdf', compact('penjualan'))
                  ->setPaper('A4', 'landscape');

        return $pdf->download('laporan-penjualan.pdf');
    }
}
