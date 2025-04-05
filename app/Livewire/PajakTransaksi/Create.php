<?php

namespace App\Livewire\PajakTransaksi;

use Livewire\Component;
use App\Models\PajakTransaksi;
use App\Models\TransaksiPembelian;
use App\Models\TransaksiPenjualan;

class Create extends Component
{
    public $open = false;

    public $jenis_transaksi;
    public $id_transaksi;
    public $persentase_pajak;
    public $nilai_pajak;

    public $listTransaksi = [];

    // Rules validasi
    protected $rules = [
        'jenis_transaksi' => 'required|in:pembelian,penjualan',
        'id_transaksi' => 'required|integer',
        'persentase_pajak' => 'required|numeric|min:0',
        'nilai_pajak' => 'required|numeric|min:0',
    ];

    // Auto kalkulasi saat persentase berubah
    public function updatedPersentasePajak()
    {
        $this->hitungPajak();
    }

    // Update list transaksi sesuai jenis
    public function updatedJenisTransaksi()
    {
        $this->getListTransaksi();
        $this->id_transaksi = null;
        $this->nilai_pajak = null;
    }

    // Kalkulasi ulang saat pilih transaksi
    public function updatedIdTransaksi()
    {
        $this->hitungPajak();
    }

    // Ambil data transaksi sesuai jenis
    public function getListTransaksi()
    {
        if ($this->jenis_transaksi === 'pembelian') {
            $this->listTransaksi = TransaksiPembelian::all();
        } elseif ($this->jenis_transaksi === 'penjualan') {
            $this->listTransaksi = TransaksiPenjualan::all();
        } else {
            $this->listTransaksi = [];
        }
    }

    // Hitung nilai pajak
    public function hitungPajak()
    {
        if (!$this->id_transaksi || !$this->persentase_pajak) return;

        if ($this->jenis_transaksi === 'pembelian') {
            $transaksi = TransaksiPembelian::find($this->id_transaksi);
        } elseif ($this->jenis_transaksi === 'penjualan') {
            $transaksi = TransaksiPenjualan::find($this->id_transaksi);
        }

        if (isset($transaksi) && $transaksi->total_nilai_transaksi) {
            $this->nilai_pajak = round(($this->persentase_pajak / 100) * $transaksi->total_nilai_transaksi, 2);
        } else {
            $this->nilai_pajak = 0;
        }
    }

    // Simpan data pajak
    public function store()
    {
        $this->validate();

        PajakTransaksi::create([
            'id_transaksi' => $this->id_transaksi,
            'jenis_transaksi' => $this->jenis_transaksi,
            'persentase_pajak' => $this->persentase_pajak,
            'nilai_pajak' => $this->nilai_pajak,
        ]);

        // Reset field form (biar gak reset semuanya)
        $this->reset(['jenis_transaksi', 'id_transaksi', 'persentase_pajak', 'nilai_pajak']);
        $this->open = false;

        // Emit untuk refresh tabel
        $this->dispatch('refreshDatatable');
    }

    public function render()
    {
        return view('livewire.pajak-transaksi.create', [
            'listTransaksi' => $this->listTransaksi,
        ]);
    }
}
