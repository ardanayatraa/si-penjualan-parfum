<?php

namespace App\Livewire\ReturnBarang;

use Livewire\Component;
use App\Models\ReturnBarang;
use App\Models\Barang;
use App\Models\Supplier;

class Create extends Component
{
    public $open = false;
    public $id_barang;
    public $id_supplier;
    public $jumlah = 1;
    public $alasan;
    public $tanggal_return;

    protected $rules = [
        'id_barang'       => 'required|exists:barang,id',
        'id_supplier'     => 'required|exists:suppliers,id',
        'jumlah'          => 'required|integer|min:1',
        'alasan'          => 'required|string|max:500',
        'tanggal_return'  => 'required|date',
    ];

    public function updatedJumlah($value)
    {
        if ($this->id_barang) {
            $barang = Barang::find($this->id_barang);
            if ($barang && $value > $barang->stok) {
                $this->addError(
                    'jumlah',
                    "Jumlah return tidak boleh melebihi stok saat ini ({$barang->stok})."
                );
                $this->jumlah = $barang->stok;
            } else {
                $this->resetErrorBag('jumlah');
            }
        }
    }

    public function store()
    {
        $this->validate();

        $barang = Barang::find($this->id_barang);
        if (! $barang) {
            $this->addError('id_barang', 'Barang tidak ditemukan.');
            return;
        }

        if ($this->jumlah > $barang->stok) {
            $this->addError(
                'jumlah',
                "Jumlah return tidak boleh melebihi stok saat ini ({$barang->stok})."
            );
            return;
        }

        // Simpan return
        ReturnBarang::create([
            'id_barang'      => $this->id_barang,
            'id_supplier'    => $this->id_supplier,
            'jumlah'         => $this->jumlah,
            'alasan'         => $this->alasan,
            'tanggal_return' => $this->tanggal_return,
        ]);

        // Kurangi stok barang sesuai jumlah return
        $barang->decrement('stok', $this->jumlah);

        $this->reset(['id_barang', 'id_supplier', 'jumlah', 'alasan', 'tanggal_return']);
        $this->dispatch('refreshDatatable');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.return-barang.create', [
            'listBarang'   => Barang::all(),
            'listSupplier' => Supplier::all(),
        ]);
    }
}
