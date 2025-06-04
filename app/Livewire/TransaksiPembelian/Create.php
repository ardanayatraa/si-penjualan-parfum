<?php

namespace App\Livewire\TransaksiPembelian;

use Livewire\Component;
use App\Models\TransaksiPembelian;
use App\Models\Barang;
use App\Models\Supplier;

class Create extends Component
{
    public $open            = false;
    public $id_supplier;
    public $tanggal_transaksi;
    public $quantities      = []; // [barang_id => jumlah]
    public $search          = '';
    public $listSupplier    = [];

    public function mount()
    {
        $this->listSupplier = Supplier::all();
    }

    public function updatedIdSupplier($value)
    {
        // inisialisasi quantities untuk setiap barang baru
        $this->quantities = [];
        $barangs = Barang::where('id_supplier', $value)->pluck('id');
        foreach ($barangs as $id) {
            $this->quantities[$id] = 0;
        }
    }

    public function updatedSearch()
    {
        // tidak perlu inisialisasi ulang quantities di sini
    }

    public function increase($barangId)
    {
        $this->quantities[$barangId] = ($this->quantities[$barangId] ?? 0) + 1;
    }

    public function decrease($barangId)
    {
        if (($this->quantities[$barangId] ?? 0) > 0) {
            $this->quantities[$barangId]--;
        }
    }

    public function store()
    {
        $this->validate([
            'id_supplier'       => 'required|exists:supplier,id_supplier',
            'tanggal_transaksi' => 'required|date',
        ]);

        $barangDipilih = collect($this->listBarang)
            ->filter(fn($b) => ($this->quantities[$b->id] ?? 0) > 0);

        if ($barangDipilih->isEmpty()) {
            $this->addError('quantities', 'Minimal satu barang harus diisi jumlah > 0.');
            return;
        }

        foreach ($barangDipilih as $barang) {
            $jumlah = $this->quantities[$barang->id];
            $total  = $barang->harga_beli * $jumlah;

            TransaksiPembelian::create([
                'id_barang'         => $barang->id,
                'tanggal_transaksi' => $this->tanggal_transaksi,
                'jumlah_pembelian'  => $jumlah,
                'total'             => $total,
            ]);

            $barang->increment('stok', $jumlah);
        }

        $this->reset(['id_supplier', 'tanggal_transaksi', 'quantities', 'search']);
        $this->dispatch('refreshDatatable');
        $this->open = false;
    }

    public function getListBarangProperty()
    {
        if (! $this->id_supplier) {
            return collect();
        }

        return Barang::where('id_supplier', $this->id_supplier)
            ->where('nama_barang', 'like', '%' . $this->search . '%')
            ->orderBy('nama_barang')
            ->get();
    }

    public function render()
    {
        return view('livewire.transaksi-pembelian.create', [
            'suppliers' => $this->listSupplier,
            'listBarang' => $this->listBarang,
        ]);
    }
}
