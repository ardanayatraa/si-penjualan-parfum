<div>
    <x-button wire:click="$set('open', true)" class="bg-indigo-600 hover:bg-indigo-700">
        Tambah Transaksi Penjualan
    </x-button>

    <x-dialog-modal wire:model="open">
        <x-slot name="title">Tambah Transaksi Penjualan</x-slot>

        <x-slot name="content">
            <div class="flex flex-col gap-4">
                {{-- Pilih Barang --}}
                <div>
                    <x-label for="id_barang" value="Barang" />
                    <select id="id_barang" wire:model.live="id_barang" class="w-full border-gray-300 rounded-md">
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

                {{-- Jumlah Penjualan --}}
                <div>
                    <x-label for="jumlah_penjualan" value="Jumlah Penjualan" />
                    <x-input id="jumlah_penjualan" type="number" wire:model.live="jumlah_penjualan" min="1"
                        class="w-full border-gray-300 rounded-md" />
                    @error('jumlah_penjualan')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Tanggal Transaksi --}}
                <div>
                    <x-label for="tanggal_transaksi" value="Tanggal Transaksi" />
                    <x-input id="tanggal_transaksi" type="date" wire:model="tanggal_transaksi"
                        class="w-full border-gray-300 rounded-md" />
                    @error('tanggal_transaksi')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Pajak --}}
                <div>
                    <x-label for="id_pajak" value="Pajak" />
                    <select id="id_pajak" wire:model="id_pajak"
                        class="w-full border-gray-300 rounded-md pointer-events-none bg-gray-100">
                        <option value="">-- Tanpa Pajak --</option>
                        @foreach ($listPajak as $p)
                            <option value="{{ $p->id }}">{{ $p->nama }} ({{ $p->presentase }}%)</option>
                        @endforeach
                    </select>

                </div>

                {{-- Hasil Perhitungan --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-label value="Harga Jual (Rp)" />
                        <x-input type="text" readonly :value="number_format($harga_jual * $jumlah_penjualan, 0, ',', '.')" class="w-full bg-gray-100 rounded-md" />
                    </div>
                    <div>
                        <x-label value="Total Harga (+ Pajak) (Rp)" />
                        <x-input type="text" readonly :value="number_format($total_harga, 0, ',', '.')" class="w-full bg-gray-100 rounded-md" />
                    </div>
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
