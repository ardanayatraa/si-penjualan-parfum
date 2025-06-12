<div>
    <x-button wire:click="$set('open', true)" class="bg-green-600 hover:bg-green-700">
        Tambah Transaksi Pembelian
    </x-button>

    <x-dialog-modal wire:model="open">
        <x-slot name="title">Tambah Transaksi Pembelian</x-slot>
        <x-slot name="content">
            <div class="space-y-6">
                {{-- FILTER SUPPLIER --}}
                <div>
                    <x-label value="Filter Supplier" />
                    <select wire:model.live="filterSupplier" class="w-full border-gray-300 rounded-md">
                        <option value="">-- Pilih Supplier --</option>
                        @foreach ($suppliers as $s)
                            <option value="{{ $s->id_supplier }}">{{ $s->nama_supplier }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- TANGGAL --}}
                <div>
                    <x-label value="Tanggal Transaksi" />
                    <x-input type="date" wire:model="tanggal_transaksi" class="w-full border-gray-300 rounded-md" />
                    @error('tanggal_transaksi')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                {{-- PENCARIAN --}}
                @if ($filterSupplier)
                    <div>
                        <x-label value="Cari Barang" />
                        <x-input type="text" wire:model.debounce.500ms="search" placeholder="Ketik nama..."
                            class="w-full border-gray-300 rounded-md" />
                    </div>
                @endif

                {{-- DAFTAR BARANG --}}
                @if ($filterSupplier)
                    <div class="overflow-y-auto" style="max-height:300px;">
                        <table class="w-full border-collapse table-auto">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="px-2 py-1 text-left">Nama Barang</th>
                                    <th class="px-2 py-1 text-center">Stok</th>
                                    <th class="px-2 py-1 text-center">Harga Beli</th>
                                    <th class="px-2 py-1 text-center">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($listBarang as $b)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-2 py-1 border">{{ $b->nama_barang }}</td>
                                        <td class="px-2 py-1 border text-center">{{ $b->stok }}</td>
                                        <td class="px-2 py-1 border text-center">
                                            Rp {{ number_format($b->harga_beli, 0, ',', '.') }}
                                        </td>
                                        <td class="px-2 py-1 border text-center">
                                            <div class="inline-flex items-center space-x-2">
                                                <button wire:click="decrease({{ $b->id }})"
                                                    class="px-2 py-1 bg-gray-200 rounded">−</button>
                                                <input type="number" wire:model="quantities.{{ $b->id }}"
                                                    min="0" class="w-16 text-center border rounded" />
                                                <button wire:click="increase({{ $b->id }})"
                                                    class="px-2 py-1 bg-gray-200 rounded">+</button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-4 text-center text-gray-500">
                                            Tidak ada barang.
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
            <div class="flex justify-end space-x-2">
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
