<div>
    <x-dialog-modal wire:model="open">
        <x-slot name="title">Edit Akun</x-slot>
        <x-slot name="content">
            <div class="flex flex-col gap-4">
                <div>
                    <x-label for="kode_akun" value="Kode Akun" />
                    <x-input id="kode_akun" wire:model.defer="kode_akun" />
                    @error('kode_akun')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <x-label for="nama_akun" value="Nama Akun" />
                    <x-input id="nama_akun" wire:model.defer="nama_akun" />
                    @error('nama_akun')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <x-label for="tipe_akun" value="Tipe Akun" />
                    <x-input id="tipe_akun" wire:model.defer="tipe_akun" />
                    @error('tipe_akun')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <x-label for="parent_id" value="Parent Akun (opsional)" />
                    <select id="parent_id" wire:model.defer="parent_id" class="w-full border-gray-300 rounded-md">
                        <option value="">-- Tidak Ada --</option>
                        @foreach ($listParent as $p)
                            <option value="{{ $p->id }}">{{ $p->nama_akun }} ({{ $p->kode_akun }})</option>
                        @endforeach
                    </select>
                    @error('parent_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <div class="flex justify-end gap-2">
                <x-button wire:click="update" class="bg-blue-600 hover:bg-blue-700">Simpan</x-button>
                <x-button wire:click="$set('open', false)" class="bg-gray-500 hover:bg-gray-600">Batal</x-button>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>
