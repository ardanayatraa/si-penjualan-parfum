<?php

namespace App\Livewire\TransaksiPenjualan;

use Livewire\Component;
use App\Models\Barang;
use App\Models\TransaksiPenjualan;
use Illuminate\Support\Facades\Auth;

class Create extends Component
{
    public $open = false;

    public $id_barang, $tanggal_transaksi, $jumlah, $harga_jual, $total_harga, $total_nilai_transaksi, $laba_bruto, $laba_bersih, $keterangan;
    public $harga_beli,$nama_user;

    protected $rules = [
        'id_barang' => 'required|integer',
        'tanggal_transaksi' => 'required|date',
        'jumlah' => 'required|numeric|min:1',
        'total_harga' => 'required|numeric|min:0',
        'total_nilai_transaksi' => 'required|numeric|min:0',
        'laba_bruto' => 'required|numeric',
        'laba_bersih' => 'required|numeric',
        'keterangan' => 'nullable|string|max:255',
    ];

    public function mount()
    {
        $this->nama_user = Auth::id();
    }
    public function updatedIdBarang()
    {
        $barang = Barang::find($this->id_barang);

        if ($barang) {
            $this->harga_jual = $barang->harga_jual;
            $this->harga_beli = $barang->harga_beli;
            $this->hitungTotal();
        }
    }

    public function updatedJumlah()
    {
        $this->hitungTotal();
    }

    public function hitungTotal()
    {
        $this->total_harga = $this->harga_jual * $this->jumlah;
        $this->total_nilai_transaksi = $this->total_harga;

        $this->laba_bruto = ($this->harga_jual - $this->harga_beli) * $this->jumlah;
        $this->laba_bersih = $this->laba_bruto;
    }

    public function store()
    {
        $this->validate();

        TransaksiPenjualan::create([
            'id_kasir' => Auth::id(),
            'id_barang' => $this->id_barang,
            'tanggal_transaksi' => $this->tanggal_transaksi,

            'jumlah' => $this->jumlah,
            'harga_jual' => $this->harga_jual,
            'total_harga' => $this->total_harga,
            'total_nilai_transaksi' => $this->total_nilai_transaksi,
            'laba_bruto' => $this->laba_bruto,
            'laba_bersih' => $this->laba_bersih,
            'keterangan' => $this->keterangan,
        ]);

        $this->dispatch('refreshDatatable');
        $this->reset([
            'id_barang', 'tanggal_transaksi', 'jumlah', 'harga_jual',
            'total_harga', 'total_nilai_transaksi', 'laba_bruto', 'laba_bersih', 'keterangan'
        ]);
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.transaksi-penjualan.create', [
            'listBarang' => Barang::all(),
        ]);
    }
}
