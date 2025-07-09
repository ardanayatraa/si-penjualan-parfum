<?php

namespace App\Livewire\Table;

use Livewire\Component;
use App\Models\TransaksiPenjualan;
use App\Models\TransaksiPembelian;
use App\Models\Piutang;
use App\Models\Pengeluaran;
use App\Models\Hutang;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ArusKasTable extends Component
{
    public $startDate;
    public $endDate;

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function updatedStartDate()
    {
        $this->render();
    }


    public function updatedEndDate()
    {
        $this->render();
    }

public function exportPdf()
{
    $data = $this->getArusKasData();

    $pdf = Pdf::loadView('exports.arus-kas-pdf', [
        'data' => $data,
        'startDate' => Carbon::parse($this->startDate)->format('d M Y'),
        'endDate' => Carbon::parse($this->endDate)->format('d M Y'),
    ])
    ->setPaper('a4', 'landscape')
    ->setOptions([
        'isHtml5ParserEnabled' => true,
        'isPhpEnabled' => true,
        'isRemoteEnabled' => true,
        'defaultFont' => 'DejaVu Sans',
        'fontHeightRatio' => 1.1,
        'dpi' => 150,
        'defaultPaperSize' => 'a4',
        'marginTop' => 20,
        'marginBottom' => 20,
        'marginLeft' => 15,
        'marginRight' => 15,
    ]);

    $filename = "laporan-arus-kas_{$this->startDate}_{$this->endDate}.pdf";

    return response()->streamDownload(
        fn() => print($pdf->stream()),
        $filename,
        [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]
    );
}
    private function getArusKasData()
    {
        $startDate = $this->startDate;
        $endDate = $this->endDate;

        // ARUS KAS MASUK (OPERASIONAL)
        $penjualanTunai = TransaksiPenjualan::whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->where('status', 'selesai')
            ->where('metode_pembayaran', 'cash')
            ->sum('total_harga');

        $penjualanNonTunai = TransaksiPenjualan::whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->where('status', 'selesai')
            ->whereNotIn('metode_pembayaran', ['cash'])
            ->sum('total_harga');

        $penagihanPiutang = Piutang::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'lunas')
            ->sum('jumlah');

        // ARUS KAS KELUAR (OPERASIONAL)
        $pembelianTunai = TransaksiPembelian::whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->where('status', 'selesai')
            ->where('metode_pembayaran', 'cash')
            ->sum('total');

        $pembelianNonTunai = TransaksiPembelian::whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->where('status', 'selesai')
            ->whereNotIn('metode_pembayaran', ['cash'])
            ->sum('total');

        $pengeluaranOperasional = Pengeluaran::whereBetween('tanggal', [$startDate, $endDate])
            ->sum('jumlah');

        $pembayaranHutang = Hutang::whereBetween('updated_at', [$startDate, $endDate])
            ->where('status', 'lunas')
            ->sum('jumlah');

        // HITUNG TOTALS
        $totalKasMasuk = $penjualanTunai + $penjualanNonTunai + $penagihanPiutang;
        $totalKasKeluar = $pembelianTunai + $pembelianNonTunai + $pengeluaranOperasional + $pembayaranHutang;
        $arusKasBersih = $totalKasMasuk - $totalKasKeluar;

        return [
            'kasMasuk' => [
                [
                    'keterangan' => 'Penjualan Tunai',
                    'jumlah' => $penjualanTunai,
                ],
                [
                    'keterangan' => 'Penjualan Non-Tunai',
                    'jumlah' => $penjualanNonTunai,
                ],
                [
                    'keterangan' => 'Penagihan Piutang',
                    'jumlah' => $penagihanPiutang,
                ],
            ],
            'kasKeluar' => [
                [
                    'keterangan' => 'Pembelian Tunai',
                    'jumlah' => $pembelianTunai,
                ],
                [
                    'keterangan' => 'Pembelian Non-Tunai',
                    'jumlah' => $pembelianNonTunai,
                ],
                [
                    'keterangan' => 'Pengeluaran Operasional',
                    'jumlah' => $pengeluaranOperasional,
                ],
                [
                    'keterangan' => 'Pembayaran Hutang',
                    'jumlah' => $pembayaranHutang,
                ],
            ],
            'totals' => [
                'totalKasMasuk' => $totalKasMasuk,
                'totalKasKeluar' => $totalKasKeluar,
                'arusKasBersih' => $arusKasBersih,
            ],
        ];
    }




    public function render()
    {
        $data = $this->getArusKasData();

        return view('livewire.table.arus-kas-table', [
            'kasMasuk' => $data['kasMasuk'],
            'kasKeluar' => $data['kasKeluar'],
            'totals' => $data['totals'],
        ]);
    }
}
