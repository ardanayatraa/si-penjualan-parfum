<?php

namespace App\Livewire\TransaksiPembelian;

use Livewire\Component;
use App\Models\TransaksiPembelian;
use App\Models\Barang;
use App\Models\Supplier;

class Create extends Component
{
    public $open = false;

    public $id_barang, $id_supplier, $tanggal, $jumlah, $total_harga_beli, $total_nilai_transaksi, $keterangan;
    public $nama_barang, $harga_beli;

    protected $rules = [
        'id_barang' => 'required|integer',
        'id_supplier' => 'required|integer',
        'tanggal' => 'required|date',
        'jumlah' => 'required|numeric|min:1',
        'harga_beli' => 'required|numeric|min:0',
        'total_harga_beli' => 'required|numeric|min:0',
        'total_nilai_transaksi' => 'required|numeric|min:0',
        'keterangan' => 'nullable|string|max:255',
    ];

    public function updatedIdBarang()
    {
        $barang = Barang::find($this->id_barang);

        if ($barang) {
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
        $this->total_harga_beli = $this->harga_beli * $this->jumlah;
        $this->total_nilai_transaksi = $this->total_harga_beli;
    }

    public function store()
    {
        $this->validate();

        TransaksiPembelian::create([
            'id_barang' => $this->id_barang,
            'id_supplier' => $this->id_supplier,
            'tanggal' => $this->tanggal,
            'harga_beli' => $this->harga_beli,
            'jumlah' => $this->jumlah,
            'total_harga_beli' => $this->total_harga_beli,
            'total_nilai_transaksi' => $this->total_nilai_transaksi,
            'keterangan' => $this->keterangan,
        ]);

        $this->dispatch('refreshDatatable');
        $this->reset(['id_barang', 'id_supplier', 'tanggal', 'jumlah', 'harga_beli', 'total_harga_beli', 'total_nilai_transaksi', 'keterangan']);
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.transaksi-pembelian.create', [
            'listBarang' => Barang::all(),
            'listSupplier' => Supplier::all(),
        ]);
    }
}
