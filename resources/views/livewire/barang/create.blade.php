<!-- resources/views/livewire/barang/create.blade.php -->
<div>
    <x-button wire:click="$set('open', true)" class="bg-green-600 hover:bg-green-700">
        Tambah Barang
    </x-button>

    <x-dialog-modal wire:model="open">
        <x-slot name="title">Tambah Barang</x-slot>
        <x-slot name="content">
            <div class="flex flex-col gap-4">
                <div>
                    <x-label for="id_supplier" value="Supplier" />
                    <select id="id_supplier" wire:model.defer="id_supplier" class="w-full border-gray-300 rounded-md">
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
                    <x-label for="nama_barang" value="Nama Barang" />
                    <x-input id="nama_barang" wire:model.defer="nama_barang" />
                    @error('nama_barang')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <x-label for="satuan" value="Satuan" />
                    <x-input id="satuan" type="text" wire:model.defer="satuan" />
                    @error('satuan')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <x-label for="harga_beli" value="Harga Beli" />
                    <x-input id="harga_beli" type="number" wire:model.defer="harga_beli" />
                    @error('harga_beli')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <x-label for="harga_jual" value="Harga Jual" />
                    <x-input id="harga_jual" type="number" wire:model.defer="harga_jual" />
                    @error('harga_jual')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <x-label for="stok" value="Stok" />
                    <x-input id="stok" type="number" wire:model.defer="stok" />
                    @error('stok')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <div class="flex justify-end gap-2">
                <x-button wire:click="store" class="bg-blue-600 hover:bg-blue-700">Simpan</x-button>
                <x-button wire:click="$set('open', false)" class="bg-gray-500 hover:bg-gray-600">Batal</x-button>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>
