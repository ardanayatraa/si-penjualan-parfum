<?php

namespace App\Livewire\Barang;

use Livewire\Component;
use App\Models\Barang;

class Update extends Component
{
    public $open = false;
    public $id_barang;
    public $nama_barang, $harga_beli, $harga_jual, $jumlah_retur, $jumlah_terjual, $jumlah_stok, $jumlah_nilai_stok, $keterangan;

    protected $listeners = ['edit' => 'loadData'];

    protected $rules = [
        'nama_barang' => 'required|string|max:255',
        'harga_beli' => 'required|numeric',
        'harga_jual' => 'required|numeric',
        'jumlah_retur' => 'nullable|integer',
        'jumlah_terjual' => 'nullable|integer',
        'jumlah_stok' => 'nullable|integer',
        'jumlah_nilai_stok' => 'nullable|numeric',
        'keterangan' => 'nullable|string',
    ];

    public function loadData($id)
    {
        $barang = Barang::findOrFail($id);

        $this->id_barang = $barang->id;
        $this->nama_barang = $barang->nama_barang;
        $this->harga_beli = $barang->harga_beli;
        $this->harga_jual = $barang->harga_jual;
        $this->jumlah_retur = $barang->jumlah_retur;
        $this->jumlah_terjual = $barang->jumlah_terjual;
        $this->jumlah_stok = $barang->jumlah_stok;
        $this->jumlah_nilai_stok = $barang->jumlah_nilai_stok;
        $this->keterangan = $barang->keterangan;

        $this->open = true;
    }

    public function update()
    {
        $this->validate();

        Barang::where('id', $this->id_barang)->update([
            'nama_barang' => $this->nama_barang,
            'harga_beli' => $this->harga_beli,
            'harga_jual' => $this->harga_jual,
            'jumlah_retur' => $this->jumlah_retur,
            'jumlah_terjual' => $this->jumlah_terjual,
            'jumlah_stok' => $this->jumlah_stok,
            'jumlah_nilai_stok' => $this->jumlah_nilai_stok,
            'keterangan' => $this->keterangan,
        ]);

        $this->reset();
        $this->dispatch('refreshDatatable');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.barang.update');
    }
}
