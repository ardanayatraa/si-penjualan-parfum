<?php

namespace App\Livewire\Piutang;

use Livewire\Component;
use App\Models\Piutang;
use App\Models\TransaksiPenjualan;

class Update extends Component
{
    public $open = false;
    public $piutang_id;
    public $id_penjualan, $nama_pelanggan, $no_telp, $jumlah, $jumlah_dibayarkan, $status;

    protected $listeners = ['editPiutang' => 'loadData'];

    protected $rules = [
        'id_penjualan'   => 'required|exists:transaksi_penjualan,id',
        'nama_pelanggan' => 'required|string|max:50',
        'no_telp'        => 'required|string|max:15',
        'jumlah'         => 'required|numeric|min:0',
        'jumlah_dibayarkan' => 'required|numeric|min:0',
        'status'         => 'required|string|max:20',
    ];

    public function loadData($id)
    {
        $data = Piutang::findOrFail($id);

        $this->piutang_id     = $data->id_piutang;
        $this->id_penjualan   = $data->id_penjualan;
        $this->nama_pelanggan = $data->nama_pelanggan;
        $this->no_telp        = $data->no_telp;
        $this->jumlah         = $data->jumlah;
        $this->jumlah_dibayarkan = $data->jumlah_dibayarkan;
        $this->status         = $data->status;
        $this->open           = true;
    }

    public function update()
    {
        $this->validate();

        Piutang::where('id_piutang', $this->piutang_id)->update([
            'id_penjualan'   => $this->id_penjualan,
            'nama_pelanggan' => $this->nama_pelanggan,
            'no_telp'        => $this->no_telp,
            'jumlah'         => $this->jumlah,
            'jumlah_dibayarkan' => $this->jumlah_dibayarkan,
            'status'         => $this->status,
        ]);

        $this->reset();
        $this->dispatch('refreshDatatable');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.piutang.update', [
            'listPenjualan' => TransaksiPenjualan::all(),
        ]);
    }
}
