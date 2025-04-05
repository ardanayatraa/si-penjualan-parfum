<?php

namespace App\Livewire\PajakTransaksi;

use Livewire\Component;
use App\Models\PajakTransaksi;
use App\Models\TransaksiPenjualan;
use App\Models\TransaksiPembelian;

class Update extends Component
{
    public $open = false;
    public $pajakId;

    public $jenis_transaksi = 'penjualan';
    public $id_transaksi;
    public $persentase_pajak;
    public $nilai_pajak;

    protected $listeners = ['edit' => 'loadPajak'];

    protected $rules = [
        'jenis_transaksi' => 'required|in:penjualan,pembelian',
        'id_transaksi' => 'required|integer',
        'persentase_pajak' => 'required|numeric|min:0',
        'nilai_pajak' => 'required|numeric|min:0',
    ];

    public function loadPajak($id)
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->open = true;
        $this->pajakId = $id;

        $pajak = PajakTransaksi::findOrFail($id);
        $this->jenis_transaksi = $pajak->jenis_transaksi;
        $this->id_transaksi = $pajak->id_transaksi;
        $this->persentase_pajak = $pajak->persentase_pajak;
        $this->nilai_pajak = $pajak->nilai_pajak;
    }

    public function getListTransaksiProperty()
    {
        return $this->jenis_transaksi === 'penjualan'
            ? TransaksiPenjualan::latest()->get()
            : TransaksiPembelian::latest()->get();
    }

    public function update()
    {
        $this->validate();

        PajakTransaksi::where('id', $this->pajakId)->update([
            'id_transaksi' => $this->id_transaksi,
            'jenis_transaksi' => $this->jenis_transaksi,
            'persentase_pajak' => $this->persentase_pajak,
            'nilai_pajak' => $this->nilai_pajak,
        ]);

        $this->reset();
        $this->dispatch('refreshDatatable');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.pajak-transaksi.update', [
            'listTransaksi' => $this->listTransaksi,
        ]);
    }
}
