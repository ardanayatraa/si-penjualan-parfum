<div>
    <x-dialog-modal wire:model="open">
        <x-slot name="title">Edit Transaksi Pembelian</x-slot>
        <x-slot name="content">
            <div class="flex flex-col gap-4">

                <div>
                    <x-label value="Barang" />
                    <select wire:model.live="id_barang" class="w-full border-gray-300 rounded-md">
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
                    <x-label value="Supplier" />
                    <select wire:model.live="id_supplier" class="w-full border-gray-300 rounded-md">
                        <option value="">-- Pilih Supplier --</option>
                        @foreach ($listSupplier as $s)
                            <option value="{{ $s->id_supplier }}">{{ $s->nama_supplier }}</option>
                        @endforeach
                    </select>
                    @error('id_supplier')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <x-label value="Tanggal Transaksi" />
                    <x-input type="date" wire:model.live="tanggal_transaksi" />
                    @error('tanggal_transaksi')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-label value="Jumlah Pembelian" />
                        <x-input type="number" wire:model.live="jumlah_pembelian" min="1" />
                        @error('jumlah_pembelian')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <x-label value="Total (Rp)" />
                        <x-input type="number" wire:model="total" readonly />
                    </div>
                </div>

            </div>
        </x-slot>
        <x-slot name="footer">
            <div class="flex justify-end gap-2">
                <x-button wire:click="update" class="bg-blue-600 hover:bg-blue-700">Update</x-button>
                <x-button wire:click="$set('open', false')" class="bg-gray-500 hover:bg-gray-600">Batal</x-button>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>
