<div>
    <x-button wire:click="$set('open', true)" class="bg-green-600 hover:bg-green-700">
        Tambah Pajak Transaksi
    </x-button>

    <x-dialog-modal wire:model="open">
        <x-slot name="title">Tambah PajakTransaksi</x-slot>
        <x-slot name="content">
            <div class="flex flex-col gap-4">

                <div>
                    <x-label for="nama" value="Nama Pajak" />
                    <x-input id="nama" wire:model.defer="nama" />
                    @error('nama')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <x-label for="presentase" value="Presentase (%)" />
                    <x-input id="presentase" type="number" step="0.01" wire:model.defer="presentase" />
                    @error('presentase')
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
