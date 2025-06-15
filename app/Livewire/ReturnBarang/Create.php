<?php

namespace App\Livewire\ReturnBarang;

use Livewire\Component;
use App\Models\ReturnBarang;
use App\Models\Barang;
use App\Models\Supplier;
use App\Models\JurnalUmum;
use App\Models\DetailJurnal;
use App\Models\Akun;
use Carbon\Carbon;

class Create extends Component
{
    public $open = false;
    public $id_barang;
    public $jumlah = 1;
    public $alasan;
    public $tanggal_return;

    protected $rules = [
        'id_barang'       => 'required|exists:barang,id',
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
            $this->addError('id_barang','Barang tidak ditemukan.');
            return;
        }
        if ($this->jumlah > $barang->stok) {
            $this->addError(
                'jumlah',
                "Jumlah return tidak boleh melebihi stok saat ini ({$barang->stok})."
            );
            return;
        }

        // 1. Simpan ReturnBarang
        $return = ReturnBarang::create([
            'id_barang'      => $this->id_barang,
            'id_supplier'    => $barang->id_supplier,
            'jumlah'         => $this->jumlah,
            'alasan'         => $this->alasan,
            'tanggal_return' => $this->tanggal_return,
        ]);

        // 2. Kurangi stok
        $barang->decrement('stok', $this->jumlah);

        // 3. Buat jurnal retur pembelian
        $this->buatJurnalReturn($return, $barang);

        // 4. Reset form
        $this->reset(['id_barang','jumlah','alasan','tanggal_return']);
        $this->dispatch('refreshDatatable');
        $this->open = false;
    }

    protected function buatJurnalReturn(ReturnBarang $return, Barang $barang)
    {
        // Generate no_bukti: JR-RB-YYYYMMDD-XXX
        $tgl = Carbon::parse($return->tanggal_return)->format('Ymd');
        $urut = JurnalUmum::whereDate('tanggal', $return->tanggal_return)->count() + 1;
        $noBukti = sprintf('JR-RB-%s-%03d', $tgl, $urut);

        // Header jurnal
        $j = JurnalUmum::create([
            'tanggal'    => $return->tanggal_return,
            'no_bukti'   => $noBukti,
            'keterangan' => 'Retur Barang ID '.$return->id,
        ]);

        // Ambil akun
        $akunHutang    = Akun::where('kode_akun','2.1.01')->first(); // Hutang Dagang
        $akunPersediaan= Akun::where('kode_akun','1.1.05')->first(); // Persediaan Barang

        $nilai = $barang->harga_pokok * $return->jumlah; // nilai pokok retur

        // Debit Hutang Dagang (mengurangi hutang)
        if ($akunHutang) {
            DetailJurnal::create([
                'jurnal_umum_id'=>$j->id,
                'akun_id'       =>$akunHutang->id,
                'debit'         =>$nilai,
                'kredit'        =>0,
            ]);
        }

        // Kredit Persediaan Barang (mengembalikan persediaan)
        if ($akunPersediaan) {
            DetailJurnal::create([
                'jurnal_umum_id'=>$j->id,
                'akun_id'       =>$akunPersediaan->id,
                'debit'         =>0,
                'kredit'        =>$nilai,
            ]);
        }

        // Update referensi di return_barang (jika kolom ada)
        $return->jurnal_umum_id = $j->id;
        $return->save();
    }

    public function render()
    {
        return view('livewire.return-barang.create', [
            'listBarang' => Barang::all(),
        ]);
    }
}
