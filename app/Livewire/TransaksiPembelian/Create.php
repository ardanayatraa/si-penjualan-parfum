<?php

namespace App\Livewire\TransaksiPembelian;

use Livewire\Component;
use App\Models\TransaksiPembelian;
use App\Models\Barang;
use App\Models\Supplier;
use App\Models\JurnalUmum;
use App\Models\DetailJurnal;
use App\Models\Akun;
use Illuminate\Support\Facades\DB;

class Create extends Component
{
    public $open               = false;
    public $filterSupplier;
    public $tanggal_transaksi;
    public $quantities         = [];
    public $search             = '';
    public $listSupplier       = [];

    protected function rules()
    {
        return [
            'tanggal_transaksi'  => 'required|date',
            'quantities'         => 'required|array',
            'quantities.*'       => 'integer|min:0',
        ];
    }

    public function mount()
    {
        $this->listSupplier = Supplier::all();
    }

    public function updatedFilterSupplier($supplierId)
    {
        $this->search     = '';
        $this->quantities = [];

        $ids = Barang::where('id_supplier', $supplierId)->pluck('id');
        foreach ($ids as $id) {
            $this->quantities[$id] = 0;
        }
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
        $this->validate();

        $dipilih = collect($this->listBarang)
        ->filter(fn($b) => ($this->quantities[$b->id] ?? 0) > 0);

        if ($dipilih->isEmpty()) {
            return $this->addError('quantities', 'Minimal satu barang dengan jumlah > 0.');
        }

        DB::transaction(function() use ($dipilih) {
            foreach ($dipilih as $barang) {
                $jml   = $this->quantities[$barang->id];
                $total = $barang->harga_beli * $jml;

                // 1) simpan transaksi pembelian
                $t = TransaksiPembelian::create([
                    'id_barang'         => $barang->id,
                    'tanggal_transaksi' => $this->tanggal_transaksi,
                    'jumlah_pembelian'  => $jml,
                    'total'             => $total,
                ]);

                // 2) update stok
                $barang->increment('stok', $jml);

                // 3) buat header jurnal umum
                $j = JurnalUmum::create([
                    'tanggal'    => $this->tanggal_transaksi,
                    'no_bukti'   => 'PBJ-'.$t->id,
                    'keterangan' => "Pembelian {$barang->nama_barang}",
                ]);

                // 4) detail jurnal debit: Persediaan (akun kode 1.1.01)
                $akunPersediaan = Akun::where('kode_akun', '1.1.01')->first();
                DetailJurnal::create([
                    'jurnal_umum_id' => $j->id,
                    'akun_id'        => $akunPersediaan->id,
                    'debit'          => $total,
                    'kredit'         => 0,
                ]);

                // 5) detail jurnal kredit: Kas/Hutang (akun kode 2.1.01)
                $akunHutang = Akun::where('kode_akun', '2.1.01')->first();
                DetailJurnal::create([
                    'jurnal_umum_id' => $j->id,
                    'akun_id'        => $akunHutang->id,
                    'debit'          => 0,
                    'kredit'         => $total,
                ]);
            }
        });


        $this->reset(['filterSupplier','tanggal_transaksi','quantities','search']);
        $this->dispatch('refreshDatatable');
        $this->open = false;
    }

    public function getListBarangProperty()
    {
        if (! $this->filterSupplier) {
            return collect();
        }

        return Barang::where('id_supplier', $this->filterSupplier)
            ->when($this->search, fn($q) => $q->where('nama_barang','like','%'.$this->search.'%'))
            ->orderBy('nama_barang')
            ->get();
    }

    public function render()
    {
        return view('livewire.transaksi-pembelian.create', [
            'suppliers' => $this->listSupplier,
            'listBarang'=> $this->listBarang,
        ]);
    }
}
