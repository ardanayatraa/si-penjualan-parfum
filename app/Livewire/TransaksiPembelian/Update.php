<?php

namespace App\Livewire\TransaksiPembelian;

use Livewire\Component;
use App\Models\TransaksiPembelian;
use App\Models\Barang;
use App\Models\Supplier;
use Illuminate\Support\Facades\Gate;

class Update extends Component
{
    public $open = false;
    public $transaksi_id;

    public $id_barang, $id_supplier, $tanggal, $nama_barang, $harga_beli, $jumlah, $total_harga_beli, $total_nilai_transaksi, $keterangan;

    protected $listeners = ['edit' => 'loadData'];

    protected $rules = [
        'id_barang' => 'required|integer',
        'id_supplier' => 'required|integer',
        'tanggal' => 'required|date',
        'nama_barang' => 'required|string|max:255',
        'harga_beli' => 'required|numeric|min:0',
        'jumlah' => 'required|numeric|min:1',
        'total_harga_beli' => 'required|numeric|min:0',
        'total_nilai_transaksi' => 'required|numeric|min:0',
        'keterangan' => 'nullable|string|max:255',
    ];

    public function loadData($id)
    {
        $transaksi = TransaksiPembelian::findOrFail($id);

        $this->transaksi_id = $transaksi->id;
        $this->id_barang = $transaksi->id_barang;
        $this->id_supplier = $transaksi->id_supplier;
        $this->tanggal = $transaksi->tanggal;
        $this->nama_barang = $transaksi->nama_barang;
        $this->harga_beli = $transaksi->harga_beli;
        $this->jumlah = $transaksi->jumlah;
        $this->total_harga_beli = $transaksi->total_harga_beli;
        $this->total_nilai_transaksi = $transaksi->total_nilai_transaksi;
        $this->keterangan = $transaksi->keterangan;

        $this->open = true;
    }

    public function updatedHargaBeli()
    {
        $this->hitungTotal();
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

    public function update()
    {
        $this->validate();

        $transaksi = TransaksiPembelian::findOrFail($this->transaksi_id);

        $transaksi->update([
            'id_barang' => $this->id_barang,
            'id_supplier' => $this->id_supplier,
            'tanggal' => $this->tanggal,
            'nama_barang' => $this->nama_barang,
            'harga_beli' => $this->harga_beli,
            'jumlah' => $this->jumlah,
            'total_harga_beli' => $this->total_harga_beli,
            'total_nilai_transaksi' => $this->total_nilai_transaksi,
            'keterangan' => $this->keterangan,
        ]);

        $this->dispatch('refreshDatatable');
        $this->reset();
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.transaksi-pembelian.update', [
            'listBarang' => Barang::all(),
            'listSupplier' => Supplier::all(),
        ]);
    }
}
