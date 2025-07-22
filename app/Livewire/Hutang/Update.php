<?php

namespace App\Livewire\Hutang;

use Livewire\Component;
use App\Models\Hutang;

class Update extends Component
{
    public $open = false;
    public $id_hutang, $id_pembelian, $id_supplier, $jumlah, $jumlah_dibayarkan, $tgl_tempo, $status;

    protected $listeners = ['editHutang' => 'loadData'];

    protected $rules = [
        'id_pembelian' => 'required|exists:transaksi_pembelian,id',
        'id_supplier'  => 'required|exists:supplier,id_supplier',
        'jumlah'       => 'required|numeric|min:0',
        'jumlah_dibayarkan' => 'required|numeric|min:0',
        'tgl_tempo'    => 'required|date',
        'status'       => 'required|string|max:20',
    ];

    public function loadData($id)
    {
        $hutang = Hutang::findOrFail($id);
        $this->id_hutang    = $hutang->id_hutang;
        $this->id_pembelian = $hutang->id_pembelian;
        $this->id_supplier  = $hutang->id_supplier;
        $this->jumlah       = $hutang->jumlah;
        $this->jumlah_dibayarkan = $hutang->jumlah_dibayarkan;
        $this->tgl_tempo    = $hutang->tgl_tempo;
        $this->status       = $hutang->status;
        $this->open = true;
    }

    public function update()
    {
        $this->validate();

        Hutang::where('id_hutang', $this->id_hutang)->update([
            'id_pembelian' => $this->id_pembelian,
            'id_supplier'  => $this->id_supplier,
            'jumlah'       => $this->jumlah,
            'jumlah_dibayarkan' => $this->jumlah_dibayarkan,
            'tgl_tempo'    => $this->tgl_tempo,
            'status'       => $this->status,
        ]);

        $this->reset(['id_hutang','id_pembelian','id_supplier','jumlah','jumlah_dibayarkan','tgl_tempo','status']);
        $this->dispatch('refreshDatatable');
        $this->open = false;
    }

    public function render()
{
    return view('livewire.hutang.update', [
        'listSupplier' => \App\Models\Supplier::all(),
        'listPembelian' => \App\Models\TransaksiPembelian::all(),
    ]);
}
}
