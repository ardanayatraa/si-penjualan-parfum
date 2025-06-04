<div>
    <x-button wire:click="$set('open', true)" class="bg-green-600 hover:bg-green-700">
        Tambah Pengaturan
    </x-button>

    <x-dialog-modal wire:model="open">
        <x-slot name="title">Tambah Pengaturan</x-slot>
        <x-slot name="content">
            <div class="flex flex-col gap-4">
                <div>
                    <x-label for="nama_pengaturan" value="Nama Pengaturan" />
                    <x-input id="nama_pengaturan" wire:model.defer="nama_pengaturan" />
                    @error('nama_pengaturan')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <x-label for="nilai_pengaturan" value="Nilai Pengaturan" />
                    <x-input id="nilai_pengaturan" type="number" wire:model.defer="nilai_pengaturan" />
                    @error('nilai_pengaturan')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <x-label for="keterangan" value="Keterangan" />
                    <x-input id="keterangan" wire:model.defer="keterangan" />
                    @error('keterangan')
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
