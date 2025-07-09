<?php

namespace App\Livewire\Table;

use Livewire\Component;
use App\Models\TransaksiPenjualan;
use App\Models\Piutang;
use App\Models\Pengeluaran;
use App\Models\Hutang;

class ArusKasTable extends Component
{
    public function render()
    {
        $pemasukan = TransaksiPenjualan::sum('total_harga') + Piutang::where('status', 'lunas')->sum('jumlah');
        $pengeluaran = Pengeluaran::sum('jumlah') + Hutang::where('status', 'lunas')->sum('jumlah');
        $dataPemasukan = [
            [
                'keterangan' => 'Penjualan',
                'jumlah' => TransaksiPenjualan::sum('total_harga'),
            ],
            [
                'keterangan' => 'Piutang Masuk',
                'jumlah' => Piutang::where('status', 'lunas')->sum('jumlah'),
            ],
        ];
        $dataPengeluaran = [
            [
                'keterangan' => 'Pengeluaran',
                'jumlah' => Pengeluaran::sum('jumlah'),
            ],
            [
                'keterangan' => 'Pembayaran Hutang',
                'jumlah' => Hutang::where('status', 'lunas')->sum('jumlah'),
            ],
        ];
        return view('livewire.table.arus-kas-table', [
            'pemasukan' => $pemasukan,
            'pengeluaran' => $pengeluaran,
            'dataPemasukan' => $dataPemasukan,
            'dataPengeluaran' => $dataPengeluaran,
        ]);
    }
} 