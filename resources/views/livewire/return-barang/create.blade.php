<!-- resources/views/livewire/return-barang/create.blade.php -->
<div>
    <x-button wire:click="$set('open', true)" class="bg-green-600 hover:bg-green-700">
        Tambah Return Barang
    </x-button>

    <x-dialog-modal wire:model="open">
        <x-slot name="title">Tambah Return Barang</x-slot>
        <x-slot name="content">
            <div class="flex flex-col gap-4">
                <div>
                    <x-label value="Barang" />
                    <select wire:model.defer="id_barang" class="w-full border-gray-300 rounded-md">
                        <option value="">-- Pilih Barang --</option>
                        @foreach ($listBarang as $b)
                            <option value="{{ $b->id }}">
                                {{ $b->nama_barang }} (Stok: {{ $b->stok }})
                            </option>
                        @endforeach
                    </select>
                    @error('id_barang')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <x-label value="Supplier" />
                    <select wire:model.defer="id_supplier" class="w-full border-gray-300 rounded-md">
                        <option value="">-- Pilih Supplier --</option>
                        @foreach ($listSupplier as $s)
                            <option value="{{ $s->id }}">{{ $s->nama_supplier }}</option>
                        @endforeach
                    </select>
                    @error('id_supplier')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <x-label value="Jumlah" />
                    <x-input type="number" wire:model.lazy="jumlah" min="1"
                        class="w-full border-gray-300 rounded-md" />
                    @error('jumlah')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <x-label value="Alasan Return" />
                    <textarea wire:model.defer="alasan" class="w-full rounded-md border-gray-300 p-2"></textarea>
                    @error('alasan')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <x-label value="Tanggal Return" />
                    <x-input type="date" wire:model.defer="tanggal_return"
                        class="w-full border-gray-300 rounded-md" />
                    @error('tanggal_return')
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
