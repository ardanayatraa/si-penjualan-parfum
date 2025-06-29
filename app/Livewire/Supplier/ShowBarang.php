<?php

namespace App\Livewire\Supplier;

use Livewire\Component;
use App\Models\Supplier;
use App\Models\Barang;
use Illuminate\Support\Collection;

class ShowBarang extends Component
{
    public $open = false;

    // data supplier
    public $supplierName;
    public $alamat;
    public $no_telp;

    // daftar barang milik supplier
    public Collection $barangs;

    protected $listeners = [
        'showBarangs' => 'loadData',
    ];

    public function mount()
    {
        $this->barangs = collect();
    }

    public function loadData(int $idSupplier)
    {
        $supplier = Supplier::findOrFail($idSupplier);

        $this->supplierName = $supplier->nama_supplier;
        $this->alamat       = $supplier->alamat;
        $this->no_telp      = $supplier->no_telp;

        $ids = $supplier->id_barang ?? [];

        $this->barangs = Barang::whereIn('id', $ids)
                               ->orderBy('nama_barang')
                               ->get();

        $this->open = true;
    }

    public function render()
    {
        return view('livewire.supplier.show-barang');
    }
}
