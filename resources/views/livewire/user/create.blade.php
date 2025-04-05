<div>
    <x-button wire:click="$set('open', true)" class="bg-green-600 hover:bg-green-700">
        Tambah User
    </x-button>

    <x-dialog-modal wire:model="open">
        <x-slot name="title">
            Tambah User
        </x-slot>

        <x-slot name="content">
            <div class="flex flex-col gap-4">
                <div>
                    <x-label value="Level" />
                    <select wire:model.defer="level"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-blue-200">
                        <option value="">-- Pilih Level --</option>
                        <option value="admin">Admin</option>
                        <option value="kasir">Kasir</option>
                        <option value="pemilik">Pemilik</option>
                    </select>
                    @error('level')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>


                <div>
                    <x-label for="username" value="Username" />
                    <x-input id="username" wire:model.defer="username" />
                    @error('username')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <x-label for="password" value="Password" />
                    <x-input id="password" type="password" wire:model.defer="password" />
                    @error('password')
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

                <div>
                    <x-label for="alamat" value="Alamat" />
                    <textarea id="alamat" wire:model.defer="alamat"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-blue-200"></textarea>
                    @error('alamat')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-end gap-2 w-full">
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
