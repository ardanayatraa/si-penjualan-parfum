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

                <div>
                    <x-label for="nama_supplier" value="Nama Supplier" />
                    <x-input id="nama_supplier" wire:model.defer="nama_supplier" />
                    @error('nama_supplier')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <x-label for="alamat" value="Alamat" />
                    <x-input id="alamat" wire:model.defer="alamat" />
                    @error('alamat')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <x-label for="no_telp" value="No. Telepon" />
                    <x-input id="no_telp" wire:model.defer="no_telp" />
                    @error('no_telp')
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
