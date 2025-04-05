<div>
    <x-button wire:click="$set('open', true)" class="bg-green-600 hover:bg-green-700">
        Tambah Pajak Transaksi
    </x-button>

    <x-dialog-modal wire:model="open">
        <x-slot name="title">
            Tambah Pajak Transaksi
        </x-slot>

        <x-slot name="content">
            <div class="flex flex-col gap-4">

                <div>
                    <x-label for="jenis_transaksi" value="Jenis Transaksi" />
                    <select id="jenis_transaksi" wire:model.live="jenis_transaksi" class="form-select w-full">
                        <option value="">-- Pilih Jenis Transaksi --</option>
                        <option value="penjualan">Penjualan</option>
                        <option value="pembelian">Pembelian</option>
                    </select>
                    @error('jenis_transaksi')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <x-label for="id_transaksi" value="Pilih Transaksi" />
                    <select id="id_transaksi" wire:model.defer="id_transaksi" class="form-select w-full">
                        <option value="">-- Pilih Transaksi --</option>
                        @foreach ($listTransaksi as $trx)
                            <option value="{{ $trx->id }}">
                                {{ $trx->id }} - {{ $trx->nama_barang ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_transaksi')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <x-label for="persentase_pajak" value="Persentase Pajak (%)" />
                    <x-input id="persentase_pajak" wire:model.live="persentase_pajak" type="number" step="0.01" />
                    @error('persentase_pajak')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <x-label for="nilai_pajak" value="Nilai Pajak (Rp)" />
                    <x-input id="nilai_pajak" wire:model.defer="nilai_pajak" type="number" step="0.01" />
                    @error('nilai_pajak')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

            </div>
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-end gap-2">
                <x-button wire:click="store" class="bg-blue-600 hover:bg-blue-700">
                    Simpan
                </x-button>
                <x-button wire:click="$set('open', false)" class="bg-gray-500 hover:bg-gray-600">
                    Batal
                </x-button>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>
