<div>
    <x-button wire:click="$set('open', true)" class="bg-green-600 mb-2 hover:bg-green-700">
        Tambah Jurnal Umum
    </x-button>

    <x-dialog-modal wire:model="open" max-width="2xl">
        <x-slot name="title">Tambah Jurnal Umum</x-slot>
        <x-slot name="content">
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-label for="tanggal" value="Tanggal" />
                        <x-input type="date" id="tanggal" wire:model.defer="tanggal" />
                        @error('tanggal')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <x-label for="id_akun" value="Akun" />
                        <select id="id_akun" wire:model.defer="id_akun" class="w-full border-gray-300 rounded-md">
                            <option value="">-- Pilih Akun --</option>
                            @foreach ($listAkun as $akun)
                                <option value="{{ $akun->id_akun }}">
                                    {{ $akun->nama_akun }} ({{ $akun->kode_akun }})
                                </option>
                            @endforeach
                        </select>
                        @error('id_akun')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-label for="debit" value="Debit" />
                        <x-input type="number" step="0.01" wire:model.defer="debit" />
                        @error('debit')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <x-label for="kredit" value="Kredit" />
                        <x-input type="number" step="0.01" wire:model.defer="kredit" />
                        @error('kredit')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div>
                    <x-label for="keterangan" value="Keterangan" />
                    <x-input id="keterangan" wire:model.defer="keterangan" />
                    @error('keterangan')
                        <span class="text-red-500">{{ $message }}</span>
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
