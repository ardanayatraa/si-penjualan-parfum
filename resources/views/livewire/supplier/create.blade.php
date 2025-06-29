<div>
    <x-button wire:click="$set('open', true)" class="bg-green-600 hover:bg-green-700">
        Tambah Supplier
    </x-button>

    <x-dialog-modal wire:model="open">
        <x-slot name="title">
            Tambah Supplier
        </x-slot>

        <x-slot name="content">
            <div class="flex flex-col gap-4">

                {{-- Nama Supplier --}}
                <div>
                    <x-label for="nama_supplier" value="Nama Supplier" />
                    <x-input id="nama_supplier" wire:model.defer="nama_supplier" />
                    @error('nama_supplier')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Alamat --}}
                <div>
                    <x-label for="alamat" value="Alamat" />
                    <x-input id="alamat" wire:model.defer="alamat" />
                    @error('alamat')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                {{-- No. Telepon --}}
                <div>
                    <x-label for="no_telp" value="No. Telepon" />
                    <x-input id="no_telp" wire:model.defer="no_telp" />
                    @error('no_telp')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Pilih Barang --}}
                <div>
                    <x-label value="Pilih Barang" />
                    <div class="mt-1 grid grid-cols-2 gap-2 max-h-48 overflow-auto border p-2 rounded">
                        @foreach ($barangs as $barang)
                            <label class="inline-flex items-center">
                                <input type="checkbox" wire:model.defer="id_barang" value="{{ $barang->id }}"
                                    class="form-checkbox h-4 w-4 text-indigo-600" />
                                <span class="ml-2 text-gray-700">
                                    {{ $barang->nama_barang }} (Stok: {{ $barang->stok }})
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @error('id_barang')
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
