<div>
    <x-dialog-modal wire:model="open">
        <x-slot name="title">Edit Transaksi Pembelian</x-slot>

        <x-slot name="content">
            <div class="flex flex-col gap-4">
                <div>
                    <x-label value="Barang" />
                    <select wire:model="id_barang" class="w-full border-gray-300 rounded-md">
                        <option value="">-- Pilih Barang --</option>
                        @foreach ($listBarang as $barang)
                            <option value="{{ $barang->id_barang }}">{{ $barang->nama_barang }}</option>
                        @endforeach
                    </select>
                    @error('id_barang')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <x-label value="Supplier" />
                    <select wire:model="id_supplier" class="w-full border-gray-300 rounded-md">
                        <option value="">-- Pilih Supplier --</option>
                        @foreach ($listSupplier as $supplier)
                            <option value="{{ $supplier->id_supplier }}">{{ $supplier->nama_supplier }}</option>
                        @endforeach
                    </select>
                    @error('id_supplier')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <x-label value="Tanggal" />
                    <x-input type="date" wire:model="tanggal" />
                    @error('tanggal')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <x-label value="Nama Barang" />
                    <x-input wire:model="nama_barang" />
                    @error('nama_barang')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-label value="Harga Beli" />
                        <x-input type="number" wire:model="harga_beli" />
                        @error('harga_beli')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <x-label value="Jumlah" />
                        <x-input type="number" wire:model="jumlah" />
                        @error('jumlah')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div>
                    <x-label value="Total Harga Beli" />
                    <x-input type="number" wire:model="total_harga_beli" readonly />
                    @error('total_harga_beli')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <x-label value="Total Nilai Transaksi" />
                    <x-input type="number" wire:model="total_nilai_transaksi" readonly />
                    @error('total_nilai_transaksi')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <x-label value="Keterangan" />
                    <x-input wire:model="keterangan" />
                    @error('keterangan')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-button wire:click="update" class="bg-blue-600 hover:bg-blue-700">Update</x-button>
            <x-button wire:click="$set('open', false)" class="bg-gray-500 hover:bg-gray-600">Tutup</x-button>
        </x-slot>
    </x-dialog-modal>
</div>
