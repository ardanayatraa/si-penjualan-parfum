<?php

namespace App\Livewire\Table;

use App\Models\Barang;
use App\Models\TransaksiPembelian;
use App\Models\TransaksiPenjualan;
use App\Models\Pengaturan;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LaporanNeracaTable extends DataTableComponent
{
    // Jika anda ingin pengguna bisa memilih “as of date” lewat filter,
    // tambahkan public $asOfDate dan DateFilter. Tetapi di contoh ini
    // kita tampilkan “per hari ini” secara default tanpa filter.

    public function configure(): void
    {
        $this->setPrimaryKey('akun');
    }

    public function builder(): Builder
    {
        // Hitung tanggal hari ini
        $tanggal = now()->format('Y-m-d');

        // 1. Persediaan (nilai seluruh stok x harga_beli)
        $persediaan = Barang::sum(DB::raw('stok * harga_beli'));

        // 2. Piutang (total penjualan sampai hari ini)
        $piutang = TransaksiPenjualan::whereDate('tanggal_transaksi', '<=', $tanggal)
            ->sum('total_harga');

        // 3. Hutang (total pembelian sampai hari ini)
        $hutang = TransaksiPembelian::whereDate('tanggal_pembelian', '<=', $tanggal)
            ->sum('total');

        // 4. Total penjualan & pembelian sampai hari ini (untuk laba rugi)
        $totalPenjualan = TransaksiPenjualan::whereDate('tanggal_transaksi', '<=', $tanggal)
            ->sum('total_harga');
        $totalPembelian = TransaksiPembelian::whereDate('tanggal_pembelian', '<=', $tanggal)
            ->sum('total');
        $labaRugi = $totalPenjualan - $totalPembelian;

        // 5. Kas = Laba/Rugi
        $kas = $labaRugi;

        // 6. Modal Awal dari Pengaturan
        $modalAwal = Pengaturan::where('nama_pengaturan', 'modal_awal')
            ->value('nilai_pengaturan') ?? 0;

        // 7. Susun array neraca
        $baris = [
            ['akun' => 'Kas',                'nilai' => (float)$kas,         'jenis' => 'Aktiva'],
            ['akun' => 'Persediaan Barang',  'nilai' => (float)$persediaan,  'jenis' => 'Aktiva'],
            ['akun' => 'Piutang Usaha',      'nilai' => (float)$piutang,     'jenis' => 'Aktiva'],
            ['akun' => 'Hutang Usaha',       'nilai' => (float)$hutang,      'jenis' => 'Pasiva'],
            ['akun' => 'Modal Pemilik',      'nilai' => (float)$modalAwal,   'jenis' => 'Pasiva'],
            ['akun' => 'Laba/Rugi Berjalan', 'nilai' => (float)$labaRugi,    'jenis' => 'Pasiva'],
        ];

        // 8. Bangun SQL UNION ALL
        $subqueries = collect($baris)->map(function ($item) {
            $akun  = addslashes($item['akun']);
            $nilai = $item['nilai'];
            $jenis = $item['jenis'];
            return "SELECT '{$akun}' AS akun, {$nilai} AS nilai, '{$jenis}' AS jenis";
        })->implode(' UNION ALL ');

        $sql = "({$subqueries}) AS neraca";

        return DB::table(DB::raw($sql));
    }

    public function columns(): array
    {
        return [
            Column::make('Akun', 'akun')
                ->sortable()
                ->searchable(),

            Column::make('Jenis', 'jenis')
                ->sortable(),

            Column::make('Nilai (Rp)', 'nilai')
                ->sortable()
                ->format(fn($v) => 'Rp '.number_format($v, 0, ',', '.')),
        ];
    }

    public function bulkActions(): array
    {
        return ['exportPdf' => 'Export PDF'];
    }

    public function exportPdf()
    {
        // Hitung ulang data neraca
        $tanggal = now()->format('Y-m-d');

        $persediaan = Barang::sum(DB::raw('stok * harga_beli'));
        $piutang = TransaksiPenjualan::whereDate('tanggal_transaksi', '<=', $tanggal)
            ->sum('total_harga');
        $hutang = TransaksiPembelian::whereDate('tanggal_pembelian', '<=', $tanggal)
            ->sum('total');
        $totalPenjualan = TransaksiPenjualan::whereDate('tanggal_transaksi', '<=', $tanggal)
            ->sum('total_harga');
        $totalPembelian = TransaksiPembelian::whereDate('tanggal_pembelian', '<=', $tanggal)
            ->sum('total');
        $labaRugi = $totalPenjualan - $totalPembelian;
        $kas = $labaRugi;
        $modalAwal = Pengaturan::where('nama_pengaturan', 'modal_awal')
            ->value('nilai_pengaturan') ?? 0;

        $baris = [
            ['akun' => 'Kas',                'nilai' => (float)$kas,         'jenis' => 'Aktiva'],
            ['akun' => 'Persediaan Barang',  'nilai' => (float)$persediaan,  'jenis' => 'Aktiva'],
            ['akun' => 'Piutang Usaha',      'nilai' => (float)$piutang,     'jenis' => 'Aktiva'],
            ['akun' => 'Hutang Usaha',       'nilai' => (float)$hutang,      'jenis' => 'Pasiva'],
            ['akun' => 'Modal Pemilik',      'nilai' => (float)$modalAwal,   'jenis' => 'Pasiva'],
            ['akun' => 'Laba/Rugi Berjalan', 'nilai' => (float)$labaRugi,    'jenis' => 'Pasiva'],
        ];

        $pdf = Pdf::loadView('exports.neraca-pdf', [
            'baris'   => $baris,
            'tanggal' => Carbon::parse($tanggal)->format('d-m-Y'),
        ])->setPaper('a4','landscape');

        return response()->streamDownload(fn() => print($pdf->stream()), "laporan-neraca_{$tanggal}.pdf");
    }
}
