<div>
    <x-button wire:click="$set('open', true)" class="bg-indigo-600 hover:bg-indigo-700">
        Tambah Transaksi Penjualan
    </x-button>

    <x-dialog-modal wire:model="open">
        <x-slot name="title">Tambah Transaksi Penjualan</x-slot>

        <x-slot name="content">
            <div class="flex flex-col gap-4">

                <div>
                    <x-label value="Barang" />
                    <select wire:model.live="id_barang" class="w-full border-gray-300 rounded-md">
                        <option value="">-- Pilih Barang --</option>
                        @foreach ($listBarang as $barang)
                            <option value="{{ $barang->id }}">{{ $barang->nama_barang }}</option>
                        @endforeach
                    </select>
                    @error('id_barang')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <x-label value="Tanggal Transaksi" />
                    <x-input type="date" wire:model="tanggal_transaksi" />
                    @error('tanggal_transaksi')
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
                        <x-label value="Harga Jual" />
                        <x-input type="number" wire:model="harga_jual" readonly />
                        @error('harga_barang')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <x-label value="Jumlah" />
                        <x-input type="number" wire:model.live="jumlah" />
                        @error('jumlah')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div>
                    <x-label value="Total Harga" />
                    <x-input type="number" wire:model="total_harga" readonly />
                    @error('total_harga')
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

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-label value="Laba Bruto" />
                        <x-input type="number" wire:model="laba_bruto" readonly />
                        @error('laba_bruto')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <x-label value="Laba Bersih" />
                        <x-input type="number" wire:model="laba_bersih" readonly />
                        @error('laba_bersih')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div>
                    <x-label value="Keterangan (Opsional)" />
                    <x-input wire:model="keterangan" />
                    @error('keterangan')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

            </div>
        </x-slot>

        <x-slot name="footer">
            <x-button wire:click="store" class="bg-blue-600 hover:bg-blue-700">Simpan</x-button>
            <x-button wire:click="$set('open', false)" class="bg-gray-500 hover:bg-gray-600">Batal</x-button>
        </x-slot>
    </x-dialog-modal>
</div>
