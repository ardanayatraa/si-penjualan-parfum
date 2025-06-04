<div>
    <x-button wire:click="$set('open', true)" class="bg-green-600 hover:bg-green-700">
        Tambah Transaksi Pembelian
    </x-button>

    <x-dialog-modal wire:model="open">
        <x-slot name="title">Tambah Transaksi Pembelian</x-slot>
        <x-slot name="content">
            <div class="flex flex-col gap-6">
                {{-- PILIH SUPPLIER --}}
                <div>
                    <x-label value="Supplier" />
                    <select wire:model.live="id_supplier" class="w-full border-gray-300 rounded-md">
                        <option value="">-- Pilih Supplier --</option>
                        @foreach ($suppliers as $s)
                            <option value="{{ $s->id_supplier }}">{{ $s->nama_supplier }}</option>
                        @endforeach
                    </select>
                    @error('id_supplier')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                {{-- TANGGAL TRANSAKSI --}}
                <div>
                    <x-label value="Tanggal Transaksi" />
                    <x-input type="date" wire:model="tanggal_transaksi" class="w-full border-gray-300 rounded-md" />
                    @error('tanggal_transaksi')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                {{-- KOTAK PENCARIAN --}}
                @if ($id_supplier)
                    <div>
                        <x-label value="Cari Barang" />
                        <x-input type="text" wire:model.live="search" placeholder="Ketik nama barang..."
                            class="w-full border-gray-300 rounded-md" />
                    </div>
                @endif

                {{-- DAFTAR BARANG DENGAN SCROLL --}}
                @if ($id_supplier)
                    <div class="overflow-y-auto" style="max-height: 300px;">

                        <table class="w-full table-auto border-collapse">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border px-2 py-1 text-left">Nama Barang</th>
                                    <th class="border px-2 py-1 text-center">Stok Akhir</th>
                                    <th class="border px-2 py-1 text-center">Harga Beli</th>
                                    <th class="border px-2 py-1 text-center">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($listBarang as $b)
                                    <tr class="hover:bg-gray-50">
                                        <td class="border px-2 py-1">{{ $b->nama_barang }}</td>
                                        <td class="border px-2 py-1 text-center">{{ $b->stok }}</td>
                                        <td class="border px-2 py-1 text-center">
                                            Rp {{ number_format($b->harga_beli, 0, ',', '.') }}
                                        </td>
                                        <td class="border px-2 py-1 text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                <button type="button" wire:click="decrease({{ $b->id }})"
                                                    class="px-2 py-1 bg-gray-200 rounded">−</button>
                                                <input type="number" wire:model="quantities.{{ $b->id }}"
                                                    class="w-16 text-center border rounded" min="0" />
                                                <button type="button" wire:click="increase({{ $b->id }})"
                                                    class="px-2 py-1 bg-gray-200 rounded">+</button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-gray-500">
                                            Tidak ada barang sesuai pencarian.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        @error('quantities')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                @endif
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
