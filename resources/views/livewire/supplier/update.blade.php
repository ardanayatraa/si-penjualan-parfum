<div>
    <x-dialog-modal wire:model="open">
        <x-slot name="title">
            Edit Supplier
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
                    <textarea id="alamat" wire:model.defer="alamat"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-blue-200"></textarea>
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



            </div>
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-end gap-2">
                <x-button wire:click="update" class="bg-blue-600 hover:bg-blue-700">
                    Simpan
                </x-button>
                <x-button wire:click="$set('open', false')" class="bg-gray-500 hover:bg-gray-600">
                    Batal
                </x-button>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>
