<div>
    <x-dialog-modal wire:model="open">
        <x-slot name="title">
            Edit Barang
        </x-slot>

        <x-slot name="content">
            <div class="flex flex-col gap-4">

                <div>
                    <x-label for="nama_barang" value="Nama Barang" />
                    <x-input id="nama_barang" wire:model.defer="nama_barang" />
                    @error('nama_barang')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <x-label for="harga_beli" value="Harga Beli" />
                    <x-input id="harga_beli" type="number" wire:model.defer="harga_beli" />
                    @error('harga_beli')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <x-label for="harga_jual" value="Harga Jual" />
                    <x-input id="harga_jual" type="number" wire:model.defer="harga_jual" />
                    @error('harga_jual')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <x-label for="jumlah_retur" value="Jumlah Retur" />
                    <x-input id="jumlah_retur" type="number" wire:model.defer="jumlah_retur" />
                    @error('jumlah_retur')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <x-label for="jumlah_terjual" value="Jumlah Terjual" />
                    <x-input id="jumlah_terjual" type="number" wire:model.defer="jumlah_terjual" />
                    @error('jumlah_terjual')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <x-label for="jumlah_stok" value="Jumlah Stok" />
                    <x-input id="jumlah_stok" type="number" wire:model.defer="jumlah_stok" />
                    @error('jumlah_stok')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <x-label for="jumlah_nilai_stok" value="Jumlah Nilai Stok" />
                    <x-input id="jumlah_nilai_stok" type="number" wire:model.defer="jumlah_nilai_stok" />
                    @error('jumlah_nilai_stok')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <x-label for="keterangan" value="Keterangan" />
                    <textarea id="keterangan" wire:model.defer="keterangan"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-blue-200"></textarea>
                    @error('keterangan')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

            </div>
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-end gap-2 w-full">
                <x-button wire:click="update" class="bg-blue-600 hover:bg-blue-700">
                    Simpan
                </x-button>
                <x-button wire:click="$set('open', false)" class="bg-gray-500 hover:bg-gray-600">
                    Batal
                </x-button>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>
