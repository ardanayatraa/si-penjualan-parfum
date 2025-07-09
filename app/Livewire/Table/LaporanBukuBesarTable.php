<?php

namespace App\Livewire\Table;

use Livewire\Component;
use App\Models\Akun;
use App\Models\JurnalUmum;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class LaporanBukuBesarTable extends Component
{
    public $startDate;
    public $endDate;
    public $selectedAkun = 'all';
    public $sortBy = 'tanggal';
    public $sortDirection = 'asc';

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

    public function updatedSelectedAkun()
    {
        $this->render();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function exportPdf()
    {
        $data = $this->getBukuBesarData();
        $akunList = $this->getAkunList();

        $pdf = Pdf::loadView('exports.buku-besar-pdf', [
            'data' => $data,
            'akunList' => $akunList,
            'selectedAkun' => $this->selectedAkun,
            'startDate' => Carbon::parse($this->startDate)->format('d M Y'),
            'endDate' => Carbon::parse($this->endDate)->format('d M Y'),
            'rawStartDate' => $this->startDate,
            'rawEndDate' => $this->endDate,
        ])
        ->setPaper('a4', 'landscape')
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
            'fontHeightRatio' => 1.1,
            'dpi' => 96, // Turunkan DPI untuk performa lebih baik
            'defaultPaperSize' => 'a4',
            'marginTop' => 15,
            'marginBottom' => 15,
            'marginLeft' => 10,
            'marginRight' => 10,
        ]);

        $filename = "laporan-buku-besar_{$this->startDate}_{$this->endDate}.pdf";

        return response()->streamDownload(
            fn() => print($pdf->stream()),
            $filename,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]
        );
    }

    private function getBukuBesarData()
    {
        $query = JurnalUmum::with('akun')
            ->whereBetween('tanggal', [$this->startDate, $this->endDate]);

        if ($this->selectedAkun !== 'all') {
            $query->where('id_akun', $this->selectedAkun);
        }

        $query->orderBy($this->sortBy, $this->sortDirection);

        $jurnalEntries = $query->get();

        // Group by akun untuk menghitung saldo per akun
        $bukuBesarData = [];

        foreach ($jurnalEntries as $entry) {
            $akunId = $entry->id_akun;
            $akunData = $entry->akun;

            if (!isset($bukuBesarData[$akunId])) {
                // Hitung saldo awal berdasarkan transaksi sebelum periode
                $saldoAwal = $this->calculateSaldoAwal($akunData, $this->startDate);

                $bukuBesarData[$akunId] = [
                    'akun' => $akunData,
                    'saldo_awal' => $saldoAwal,
                    'transaksi' => [],
                    'total_debit' => 0,
                    'total_kredit' => 0,
                    'saldo_akhir' => $saldoAwal,
                ];
            }

            $bukuBesarData[$akunId]['transaksi'][] = $entry;
            $bukuBesarData[$akunId]['total_debit'] += $entry->debit;
            $bukuBesarData[$akunId]['total_kredit'] += $entry->kredit;
        }

        // Hitung saldo akhir untuk setiap akun
        foreach ($bukuBesarData as $akunId => &$data) {
            $saldoAwal = $data['saldo_awal'];
            $totalDebit = $data['total_debit'];
            $totalKredit = $data['total_kredit'];

            // Logika saldo berdasarkan tipe akun (sesuai seeder: lowercase)
            $tipeAkun = $data['akun']->tipe_akun;

            if (in_array($tipeAkun, ['aset', 'beban'])) { // lowercase
                // Saldo normal debit
                $data['saldo_akhir'] = $saldoAwal + $totalDebit - $totalKredit;
            } else {
                // Saldo normal kredit (kewajiban, pendapatan)
                $data['saldo_akhir'] = $saldoAwal + $totalKredit - $totalDebit;
            }
        }

        return $bukuBesarData;
    }

    /**
     * Hitung saldo awal akun berdasarkan transaksi sebelum periode
     */
    private function calculateSaldoAwal(Akun $akun, string $startDate): float
    {
        // Ambil saldo awal dari tabel akun
        $saldoAwal = $akun->saldo_awal ?? 0;

        // Tambahkan mutasi dari transaksi sebelum periode yang dipilih
        $tanggalSebelum = Carbon::parse($startDate)->subDay()->format('Y-m-d');

        $mutasi = JurnalUmum::where('id_akun', $akun->id_akun)
            ->whereDate('tanggal', '<=', $tanggalSebelum)
            ->selectRaw('SUM(debit) as total_debit, SUM(kredit) as total_kredit')
            ->first();

        $totalDebit = $mutasi->total_debit ?? 0;
        $totalKredit = $mutasi->total_kredit ?? 0;

        // Hitung berdasarkan tipe akun (sesuai seeder)
        if (in_array($akun->tipe_akun, ['aset', 'beban'])) {
            return $saldoAwal + $totalDebit - $totalKredit;
        } else {
            return $saldoAwal + $totalKredit - $totalDebit;
        }
    }

    private function getAkunList()
    {
        return Akun::orderBy('kode_akun', 'asc')->get();
    }

    /**
     * Get summary data untuk laporan
     */
    public function getSummaryData(): array
    {
        $bukuBesarData = $this->getBukuBesarData();

        $totalDebitKeseluruhan = 0;
        $totalKreditKeseluruhan = 0;
        $jumlahAkunAktif = 0;
        $jumlahTransaksi = 0;

        foreach ($bukuBesarData as $data) {
            $totalDebitKeseluruhan += $data['total_debit'];
            $totalKreditKeseluruhan += $data['total_kredit'];
            $jumlahTransaksi += count($data['transaksi']);

            if (count($data['transaksi']) > 0) {
                $jumlahAkunAktif++;
            }
        }

        return [
            'total_debit_keseluruhan' => $totalDebitKeseluruhan,
            'total_kredit_keseluruhan' => $totalKreditKeseluruhan,
            'jumlah_akun_aktif' => $jumlahAkunAktif,
            'jumlah_transaksi' => $jumlahTransaksi,
            'periode' => [
                'start' => $this->startDate,
                'end' => $this->endDate,
                'jumlah_hari' => Carbon::parse($this->startDate)->diffInDays(Carbon::parse($this->endDate)) + 1
            ]
        ];
    }

    public function render()
    {
        $bukuBesarData = $this->getBukuBesarData();
        $akunList = $this->getAkunList();
        $summaryData = $this->getSummaryData();

        return view('livewire.table.laporan-buku-besar-table', [
            'bukuBesarData' => $bukuBesarData,
            'akunList' => $akunList,
            'summaryData' => $summaryData,
        ]);
    }
}
