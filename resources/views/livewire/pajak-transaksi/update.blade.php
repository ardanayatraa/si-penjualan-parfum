<div>
    <x-dialog-modal wire:model="open">
        <x-slot name="title">
            Edit Pajak Transaksi
        </x-slot>

        <x-slot name="content">
            <div class="flex flex-col gap-4">

                <div>
                    <x-label value="Jenis Transaksi" />
                    <select wire:model="jenis_transaksi" class="form-select w-full">
                        <option value="penjualan">Penjualan</option>
                        <option value="pembelian">Pembelian</option>
                    </select>
                    @error('jenis_transaksi')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <x-label value="Pilih Transaksi" />
                    <select wire:model.defer="id_transaksi" class="form-select w-full">
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
                    <x-label value="Persentase Pajak (%)" />
                    <x-input type="number" wire:model.defer="persentase_pajak" step="0.01" />
                    @error('persentase_pajak')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <x-label value="Nilai Pajak (Rp)" />
                    <x-input type="number" wire:model.defer="nilai_pajak" step="0.01" />
                    @error('nilai_pajak')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

            </div>
        </x-slot>

        <x-slot name="footer">
            <x-button wire:click="update" class="bg-blue-600 hover:bg-blue-700">
                Update
            </x-button>
            <x-button wire:click="$set('open', false)" class="bg-gray-500 hover:bg-gray-600">
                Batal
            </x-button>
        </x-slot>
    </x-dialog-modal>
</div>
