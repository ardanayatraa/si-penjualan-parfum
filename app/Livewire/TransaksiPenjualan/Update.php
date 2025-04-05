<?php

namespace App\Livewire\TransaksiPenjualan;

use Livewire\Component;
use App\Models\Barang;
use App\Models\TransaksiPenjualan;

class Update extends Component
{
    public $openEdit = false;

    public $id_transaksi;
    public $id_barang, $tanggal_transaksi, $nama_barang, $jumlah, $harga_barang, $total_harga, $total_nilai_transaksi, $laba_bruto, $laba_bersih, $keterangan;
    public $harga_beli, $harga_jual;

    protected $listeners = ['edit' => 'edit'];

    protected $rules = [
        'id_barang' => 'required|integer',
        'tanggal_transaksi' => 'required|date',
        'nama_barang' => 'required|string',
        'jumlah' => 'required|numeric|min:1',
        'total_harga' => 'required|numeric|min:0',
        'total_nilai_transaksi' => 'required|numeric|min:0',
        'laba_bruto' => 'required|numeric',
        'laba_bersih' => 'required|numeric',
        'keterangan' => 'nullable|string|max:255',
    ];

    public function edit($id)
    {
        $data = TransaksiPenjualan::findOrFail($id);

        $this->id_transaksi = $id;
        $this->id_barang = $data->id_barang;
        $this->tanggal_transaksi = $data->tanggal_transaksi;
        $this->nama_barang = $data->nama_barang;
        $this->jumlah = $data->jumlah;
        $this->harga_barang = $data->harga_barang;
        $this->total_harga = $data->total_harga;
        $this->total_nilai_transaksi = $data->total_nilai_transaksi;
        $this->laba_bruto = $data->laba_bruto;
        $this->laba_bersih = $data->laba_bersih;
        $this->keterangan = $data->keterangan;

        $barang = Barang::find($this->id_barang);
        if ($barang) {
            $this->harga_jual = $barang->harga_jual;
            $this->harga_beli = $barang->harga_beli;
        }

        $this->openEdit = true;
    }

    public function updatedIdBarang()
    {
        $barang = Barang::find($this->id_barang);

        if ($barang) {
            $this->nama_barang = $barang->nama_barang;
            $this->harga_barang = $barang->harga_beli;
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

    public function update()
    {
        $this->validate();

        $barang = Barang::find($this->id_barang);
        TransaksiPenjualan::where('id', $this->id_transaksi)->update([
            'id_barang' => $this->id_barang,
            'tanggal_transaksi' => $this->tanggal_transaksi,
            'nama_barang' => $this->nama_barang,
            'jumlah' => $this->jumlah,
            'harga_barang' => $barang->harga_beli,
            'total_harga' => $this->total_harga,
            'total_nilai_transaksi' => $this->total_nilai_transaksi,
            'laba_bruto' => $this->laba_bruto,
            'laba_bersih' => $this->laba_bersih,
            'keterangan' => $this->keterangan,
        ]);

        $this->dispatch('refreshDatatable');
        $this->reset([
            'id_transaksi', 'id_barang', 'tanggal_transaksi', 'nama_barang', 'jumlah', 'harga_barang',
            'total_harga', 'total_nilai_transaksi', 'laba_bruto', 'laba_bersih', 'keterangan'
        ]);
        $this->openEdit = false;
    }

    public function render()
    {
        return view('livewire.transaksi-penjualan.update', [
            'listBarang' => Barang::all(),
        ]);
    }
}
