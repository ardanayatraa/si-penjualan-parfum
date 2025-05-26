<div>
    <x-dialog-modal wire:model="open">
        <x-slot name="title">Edit Transaksi Penjualan</x-slot>
        <x-slot name="content">
            <div class="flex flex-col gap-4">

                <div>
                    <x-label value="Barang" />
                    <select wire:model.defer="id_barang" class="w-full border-gray-300 rounded-md">
                        <option value="">-- Pilih Barang --</option>
                        @foreach ($listBarang as $b)
                            <option value="{{ $b->id }}">{{ $b->nama_barang }}</option>
                        @endforeach
                    </select>
                    @error('id_barang')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <x-label value="Pajak" />
                    <select wire:model.defer="id_pajak" class="w-full border-gray-300 rounded-md">
                        <option value="">-- Pilih Pajak --</option>
                        @foreach ($listPajak as $p)
                            <option value="{{ $p->id }}">{{ $p->nama . ' (' . $p->tarif . '%)' }}</option>
                        @endforeach
                    </select>
                    @error('id_pajak')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <x-label value="Tanggal Transaksi" />
                    <x-input type="date" wire:model.defer="tanggal_transaksi" />
                    @error('tanggal_transaksi')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <x-label value="Subtotal" />
                    <x-input type="number" wire:model.defer="subtotal" />
                    @error('subtotal')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <x-label value="Harga Pokok" />
                        <x-input type="number" wire:model="harga_pokok" readonly />
                    </div>
                    <div>
                        <x-label value="Laba Bruto" />
                        <x-input type="number" wire:model="laba_bruto" readonly />
                    </div>
                    <div>
                        <x-label value="Total Harga" />
                        <x-input type="number" wire:model="total_harga" readonly />
                    </div>
                </div>

            </div>
        </x-slot>
        <x-slot name="footer">
            <x-button wire:click="update" class="bg-blue-600 hover:bg-blue-700">Update</x-button>
            <x-button wire:click="$set('open', false)" class="bg-gray-500 hover:bg-gray-600">Batal</x-button>
        </x-slot>
    </x-dialog-modal>
</div>
