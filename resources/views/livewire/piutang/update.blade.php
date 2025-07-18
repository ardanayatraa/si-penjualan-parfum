<div>
    <x-dialog-modal wire:model="open">
        <x-slot name="title">Edit Piutang</x-slot>
        <x-slot name="content">
            <div class="space-y-4">
                <div>
                    <x-label for="id_penjualan" value="Penjualan" />
                    <select id="id_penjualan" wire:model.defer="id_penjualan" class="w-full border-gray-300 rounded-md">
                        <option value="">-- Pilih Penjualan --</option>
                        @foreach ($listPenjualan as $p)
                            <option value="{{ $p->id }}">#{{ $p->id }} - {{ $p->tanggal_transaksi }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_penjualan')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <x-label for="nama_pelanggan" value="Nama Pelanggan" />
                    <x-input id="nama_pelanggan" wire:model.defer="nama_pelanggan" type="text" />
                    @error('nama_pelanggan')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <x-label for="no_telp" value="No Telepon" />
                    <x-input id="no_telp" wire:model.defer="no_telp" type="text" />
                    @error('no_telp')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <x-label for="jumlah" value="Jumlah Piutang" />
                    <x-input id="jumlah" type="number" step="0.01" wire:model.defer="jumlah" />
                    @error('jumlah')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <x-label for="status" value="Status" />
                    <x-input id="status" wire:model.defer="status" />
                    @error('status')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-button wire:click="update" class="bg-blue-600 hover:bg-blue-700">Update</x-button>
            <x-button wire:click="$set('open', false)" class="bg-gray-500 hover:bg-gray-600">Batal</x-button>
        </x-slot>
    </x-dialog-modal>
</div>
