<div>
    <x-dialog-modal wire:model="showModal">
        <x-slot name="title">
            Edit User
        </x-slot>

        <x-slot name="content">
            <div class="space-y-4">
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
                    <x-label value="Username" />
                    <x-input wire:model.defer="username" type="text" class="w-full" />
                    @error('username')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <x-label value="Password (opsional)" />
                    <x-input wire:model.defer="password" type="password" class="w-full" />
                    @error('password')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <x-label value="No Telp" />
                    <x-input wire:model.defer="no_telp" type="text" class="w-full" />
                    @error('no_telp')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <x-label value="Alamat" />
                    <textarea wire:model.defer="alamat" class="w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-blue-200"></textarea>
                    @error('alamat')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showModal', false)">Batal</x-secondary-button>
            <x-button wire:click="update" class="ml-2">Update</x-button>
        </x-slot>
    </x-dialog-modal>
</div>
