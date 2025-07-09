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
        ])
        ->setPaper('a4', 'landscape') // Landscape untuk tabel yang lebih lebar
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
            'fontHeightRatio' => 1.1,
            'dpi' => 150,
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
                $bukuBesarData[$akunId] = [
                    'akun' => $akunData,
                    'saldo_awal' => $akunData->saldo_awal ?? 0,
                    'transaksi' => [],
                    'total_debit' => 0,
                    'total_kredit' => 0,
                    'saldo_akhir' => $akunData->saldo_awal ?? 0,
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

            // Logika saldo berdasarkan tipe akun
            $tipeAkun = $data['akun']->tipe_akun;

            if (in_array($tipeAkun, ['Aset', 'Beban'])) {
                // Saldo normal debit
                $data['saldo_akhir'] = $saldoAwal + $totalDebit - $totalKredit;
            } else {
                // Saldo normal kredit (Liabilitas, Ekuitas, Pendapatan)
                $data['saldo_akhir'] = $saldoAwal + $totalKredit - $totalDebit;
            }
        }

        return $bukuBesarData;
    }

    private function getAkunList()
    {
        return Akun::orderBy('kode_akun', 'asc')->get();
    }

    public function render()
    {
        $bukuBesarData = $this->getBukuBesarData();
        $akunList = $this->getAkunList();

        return view('livewire.table.laporan-buku-besar-table', [
            'bukuBesarData' => $bukuBesarData,
            'akunList' => $akunList,
        ]);
    }
}
