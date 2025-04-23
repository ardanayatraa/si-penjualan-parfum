<?php

namespace App\Http\Controllers;

use App\Models\TransaksiPenjualan;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    public function cetak(Request $request)
    {
        $query = TransaksiPenjualan::query();

        if ($request->start_date) {
            $query->whereDate('tanggal_transaksi', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('tanggal_transaksi', '<=', $request->end_date);
        }

        $data = $query->get();

        $pdf = Pdf::loadView('laporan.print', compact('data'))
            ->setPaper('A4', 'landscape');

        return $pdf->stream('laporan-penjualan.pdf');
    }
}
