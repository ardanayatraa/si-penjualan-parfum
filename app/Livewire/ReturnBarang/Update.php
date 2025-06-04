<?php

namespace App\Livewire\ReturnBarang;

use Livewire\Component;
use App\Models\ReturnBarang;
use App\Models\Barang;
use App\Models\Supplier;

class Update extends Component
{
    public $open = false;
    public $returnId;
    public $id_barang;
    public $id_supplier;
    public $jumlah;
    public $alasan;
    public $tanggal_return;

    // Tambahan:
    public $originalJumlah = 0;
    public $availableStok  = 0;

    protected $listeners = ['editReturn' => 'loadData'];

    protected $rules = [
        'id_barang'       => 'required|exists:barang,id',
        'id_supplier'     => 'required|exists:suppliers,id',
        'jumlah'          => 'required|integer|min:1',
        'alasan'          => 'required|string|max:500',
        'tanggal_return'  => 'required|date',
    ];

    public function loadData($id)
    {
        $r = ReturnBarang::findOrFail($id);

        $this->returnId       = $r->id;
        $this->id_barang      = $r->id_barang;
        $this->id_supplier    = $r->id_supplier;
        $this->jumlah         = $r->jumlah;
        $this->alasan         = $r->alasan;
        $this->tanggal_return = $r->tanggal_return;
        $this->originalJumlah = $r->jumlah;

        // Hitung availableStok = stok saat ini + jumlah lama
        if ($b = Barang::find($this->id_barang)) {
            $this->availableStok = $b->stok + $this->originalJumlah;
        } else {
            $this->availableStok = $this->originalJumlah;
        }

        $this->open = true;
    }

    public function updatedJumlah($value)
    {
        // Validasi agar tidak melebihi availableStok
        if ($value > $this->availableStok) {
            $this->addError(
                'jumlah',
                "Jumlah return tidak boleh melebihi stok tersedia ({$this->availableStok})."
            );
            $this->jumlah = $this->availableStok;
        } else {
            $this->resetErrorBag('jumlah');
        }
    }

    public function update()
    {
        $this->validate();

        $rOld   = ReturnBarang::findOrFail($this->returnId);
        $barang = Barang::find($this->id_barang);

        if (! $barang) {
            $this->addError('id_barang', 'Barang tidak ditemukan.');
            return;
        }

        // Hitung ulang availableStok (stok saat ini + jumlah lama)
        $maxAllow = $barang->stok + $rOld->jumlah;
        if ($this->jumlah > $maxAllow) {
            $this->addError(
                'jumlah',
                "Jumlah return tidak boleh melebihi stok tersedia ({$maxAllow})."
            );
            return;
        }

        // Sesuaikan stok:
        // - Kembalikan stok barang sesuai jumlah lama return
        $barang->increment('stok', $rOld->jumlah);
        // - Kurangi stok sesuai jumlah baru
        $barang->decrement('stok', $this->jumlah);

        // Update data di tabel return_barang
        $rOld->update([
            'id_barang'      => $this->id_barang,
            'id_supplier'    => $this->id_supplier,
            'jumlah'         => $this->jumlah,
            'alasan'         => $this->alasan,
            'tanggal_return' => $this->tanggal_return,
        ]);

        $this->reset([
            'returnId',
            'id_barang',
            'id_supplier',
            'jumlah',
            'alasan',
            'tanggal_return',
            'originalJumlah',
            'availableStok',
        ]);

        $this->dispatch('refreshDatatable');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.return-barang.update', [
            'listBarang'   => Barang::all(),
            'listSupplier' => Supplier::all(),
        ]);
    }
}
