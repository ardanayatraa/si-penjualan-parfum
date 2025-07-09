<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Barang;
use App\Models\Supplier;
use App\Models\User;
use App\Models\TransaksiPembelian;
use App\Models\TransaksiPenjualan;
use App\Models\PajakTransaksi;
use App\Models\ReturnBarang;
use Illuminate\Support\Facades\Auth;

class Admin extends Component
{
    public $items = [];

    public function mount()
    {
        $level = auth()->user()->level;

        if ($level === 'admin') {
            $this->items = [
                [
                    'label' => 'Barang',
                    'count' => Barang::count(),
                    'route' => route('barang'),
                    'color' => 'bg-blue-500',
                ],
                [
                    'label' => 'Supplier',
                    'count' => Supplier::count(),
                    'route' => route('supplier'),
                    'color' => 'bg-green-500',
                ],

                [
                    'label' => 'Return Barang',
                    'count' => ReturnBarang::count(),
                    'route' => route('return-barang'),
                    'color' => 'bg-rose-500',
                ],
                [
                    'label' => 'Penjualan',
                    'count' => TransaksiPenjualan::count(),
                    'route' => route('transaksi-penjualan'),
                    'color' => 'bg-red-500',
                ],

                [
                    'label' => 'User',
                    'count' => User::count(),
                    'route' => route('user'),
                    'color' => 'bg-purple-500',
                ],


            ];
        } elseif ($level === 'kasir') {
            $this->items = [
                [
                    'label' => 'Transaksi Penjualan',
                    'count' => TransaksiPenjualan::count(),
                    'route' => route('transaksi-penjualan'),
                    'color' => 'bg-orange-500',
                ],
                [
                    'label' => 'Transaksi Pembelian',
                    'count' => TransaksiPembelian::count(),
                    'route' => route('transaksi-pembelian'),
                    'color' => 'bg-teal-500',
                ],
            ];
        } elseif ($level === 'pemilik') {
            $this->items = [
                [
                    'label' => 'Laporan',
                    'count' => null,
                    'route' => route('laporan'),
                    'color' => 'bg-yellow-500',
                ],
                [
                    'label' => 'Grafik Penjualan',
                    'count' => null,
                    'route' => route('grafik.penjualan'),
                    'color' => 'bg-indigo-500',
                ],

            ];
        }
    }

    public function render()
    {
        return view('livewire.dashboard.admin');
    }
}
