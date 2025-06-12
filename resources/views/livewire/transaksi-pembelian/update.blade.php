<div>
    <x-dialog-modal wire:model="open">
        <x-slot name="title">Edit Transaksi Pembelian</x-slot>

        <x-slot name="content">
            <div class="flex flex-col gap-4">

                {{-- PILIH BARANG --}}
                <div>
                    <x-label value="Barang" />
                    <select wire:model="id_barang" class="w-full border-gray-300 rounded-md">
                        <option value="">-- Pilih Barang --</option>
                        @foreach ($listBarang as $b)
                            <option value="{{ $b->id }}">{{ $b->nama_barang }}</option>
                        @endforeach
                    </select>
                    @error('id_barang')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                {{-- TANGGAL --}}
                <div>
                    <x-label value="Tanggal Transaksi" />
                    <x-input type="date" wire:model.defer="tanggal_transaksi"
                        class="w-full border-gray-300 rounded-md" />
                    @error('tanggal_transaksi')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                {{-- JUMLAH & TOTAL --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-label value="Jumlah Pembelian" />
                        <x-input type="number" wire:model="jumlah_pembelian" min="1"
                            class="w-full border-gray-300 rounded-md" />
                        @error('jumlah_pembelian')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <x-label value="Total (Rp)" />
                        <x-input type="text" wire:model="total" readonly
                            class="w-full bg-gray-100 border-gray-300 rounded-md" />
                    </div>
                </div>

            </div>
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-end gap-2">
                <x-button wire:click="update" class="bg-blue-600 hover:bg-blue-700">
                    Update
                </x-button>
                <x-button wire:click="$set('open', false)" class="bg-gray-500 hover:bg-gray-600">
                    Batal
                </x-button>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>
