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
            'rawStartDate' => $this->startDate,
            'rawEndDate' => $this->endDate,
        ])
        ->setPaper('a4', 'portrait') // Ubah ke portrait untuk lebih pas
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
            'fontHeightRatio' => 1.1,
            'dpi' => 96, // Turunkan DPI untuk file lebih kecil
            'defaultPaperSize' => 'a4',
            'marginTop' => 15,
            'marginBottom' => 15,
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

        // Hitung saldo kas awal dan akhir (dari akun kas berdasarkan seeder)
        $saldoKasAwal = $this->getSaldoKasAwal($startDate);
        $saldoKasAkhir = $saldoKasAwal + $arusKasBersih;

        return [
            'kasMasuk' => [
                [
                    'keterangan' => 'Penjualan Tunai',
                    'jumlah' => $penjualanTunai,
                    'kategori' => 'operasional'
                ],
                [
                    'keterangan' => 'Penjualan Non-Tunai',
                    'jumlah' => $penjualanNonTunai,
                    'kategori' => 'operasional'
                ],
                [
                    'keterangan' => 'Penagihan Piutang',
                    'jumlah' => $penagihanPiutang,
                    'kategori' => 'operasional'
                ],
            ],
            'kasKeluar' => [
                [
                    'keterangan' => 'Pembelian Tunai',
                    'jumlah' => $pembelianTunai,
                    'kategori' => 'operasional'
                ],
                [
                    'keterangan' => 'Pembelian Non-Tunai',
                    'jumlah' => $pembelianNonTunai,
                    'kategori' => 'operasional'
                ],
                [
                    'keterangan' => 'Pengeluaran Operasional',
                    'jumlah' => $pengeluaranOperasional,
                    'kategori' => 'operasional'
                ],
                [
                    'keterangan' => 'Pembayaran Hutang',
                    'jumlah' => $pembayaranHutang,
                    'kategori' => 'pendanaan'
                ],
            ],
            'totals' => [
                'totalKasMasuk' => $totalKasMasuk,
                'totalKasKeluar' => $totalKasKeluar,
                'arusKasBersih' => $arusKasBersih,
                'saldoKasAwal' => $saldoKasAwal,
                'saldoKasAkhir' => $saldoKasAkhir,
            ],
            'metadata' => [
                'periode' => [
                    'start' => $startDate,
                    'end' => $endDate,
                    'jumlah_hari' => Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1
                ],
                'statistik' => [
                    'rata_rata_kas_masuk_harian' => $totalKasMasuk / max(1, Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1),
                    'rata_rata_kas_keluar_harian' => $totalKasKeluar / max(1, Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1),
                ]
            ]
        ];
    }

    /**
     * Get saldo kas awal berdasarkan akun kas (1.1.01) dari seeder
     */
    private function getSaldoKasAwal($tanggal)
    {
        // Ambil saldo awal kas dari akun 1.1.01 (berdasarkan seeder)
        $akunKas = \App\Models\Akun::where('kode_akun', '1.1.01')->first();

        if (!$akunKas) {
            return 0;
        }

        // Saldo awal + mutasi sampai tanggal sebelum periode
        $tanggalSebelum = Carbon::parse($tanggal)->subDay()->format('Y-m-d');

        $mutasiKas = \App\Models\JurnalUmum::where('id_akun', $akunKas->id_akun)
            ->whereDate('tanggal', '<=', $tanggalSebelum)
            ->selectRaw('SUM(debit) - SUM(kredit) as mutasi')
            ->value('mutasi') ?? 0;

        return $akunKas->saldo_awal + $mutasiKas;
    }

    public function render()
    {
        $data = $this->getArusKasData();

        return view('livewire.table.arus-kas-table', [
            'kasMasuk' => $data['kasMasuk'],
            'kasKeluar' => $data['kasKeluar'],
            'totals' => $data['totals'],
            'metadata' => $data['metadata'],
        ]);
    }
}
