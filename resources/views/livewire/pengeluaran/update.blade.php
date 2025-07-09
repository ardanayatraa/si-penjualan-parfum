<div>
    <x-dialog-modal wire:model="open">
        <x-slot name="title">Edit Pengeluaran</x-slot>
        <x-slot name="content">
            <div class="flex flex-col gap-4">
                <div>
                    <x-label for="id_akun" value="Akun Beban" />
                    <select id="id_akun" wire:model.defer="id_akun" class="w-full border-gray-300 rounded-md">
                        <option value="">-- Pilih Akun --</option>
                        @foreach ($listAkun as $a)
                            <option value="{{ $a->id_akun }}">{{ $a->nama_akun }}</option>
                        @endforeach
                    </select>
                    @error('id_akun')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <x-label for="id_user" value="Pencatat" />
                    <select id="id_user" wire:model.defer="id_user" class="w-full border-gray-300 rounded-md">
                        <option value="">-- Pilih User --</option>
                        @foreach ($listUser as $u)
                            <option value="{{ $u->id_user }}">{{ $u->username }}</option>
                        @endforeach
                    </select>
                    @error('id_user')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <x-label for="tanggal" value="Tanggal" />
                    <x-input id="tanggal" type="date" wire:model.defer="tanggal" />
                    @error('tanggal')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <x-label for="jenis_pengeluaran" value="Jenis Pengeluaran" />
                    <x-input id="jenis_pengeluaran" wire:model.defer="jenis_pengeluaran" />
                    @error('jenis_pengeluaran')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <x-label for="jumlah" value="Jumlah" />
                    <x-input id="jumlah" type="number" wire:model.defer="jumlah" />
                    @error('jumlah')
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
                <x-button wire:click="update" class="bg-blue-600 hover:bg-blue-700">Update</x-button>
                <x-button wire:click="$set('open', false)" class="bg-gray-500 hover:bg-gray-600">Batal</x-button>
            </div>
        </x-slot>
    </x-dialog-modal>
</div> 