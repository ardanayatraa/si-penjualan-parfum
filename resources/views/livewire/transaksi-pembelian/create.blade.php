<div>
    <x-button wire:click="$set('open', true)" class="bg-green-600 hover:bg-green-700">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Tambah Transaksi Pembelian
    </x-button>

    <x-dialog-modal wire:model="open" maxWidth="2xl">
        <x-slot name="title">
            <div class="flex items-center">
                <svg class="w-6 h-6 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                Tambah Transaksi Pembelian
            </div>
        </x-slot>

        <x-slot name="content">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Left Column: Form --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Error General --}}
                    @error('general')
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md">
                            {{ $message }}
                        </div>
                    @enderror

                    {{-- Supplier Selection --}}
                    <div>
                        <x-label for="id_supplier" value="Supplier *" class="font-medium" />
                        <select id="id_supplier" wire:model.live="id_supplier"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500">
                            <option value="">-- Pilih Supplier --</option>
                            @foreach ($suppliers as $s)
                                <option value="{{ $s->id_supplier }}">{{ $s->nama_supplier }}</option>
                            @endforeach
                        </select>
                        @error('id_supplier')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror

                        {{-- Selected Supplier Info --}}
                        @if ($selectedSupplier)
                            <div class="mt-2 p-3 bg-green-50 border border-green-200 rounded-md">
                                <div class="text-sm text-green-800">
                                    <div><strong>Supplier:</strong> {{ $selectedSupplier->nama_supplier }}</div>
                                    <div><strong>Alamat:</strong> {{ $selectedSupplier->alamat }}</div>
                                    <div><strong>Telepon:</strong> {{ $selectedSupplier->no_telp }}</div>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Transaction Details --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Tanggal Transaksi --}}
                        <div>
                            <x-label for="tanggal_transaksi" value="Tanggal Transaksi *" class="font-medium" />
                            <x-input id="tanggal_transaksi" type="date" wire:model="tanggal_transaksi"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500" />
                            @error('tanggal_transaksi')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Metode Pembayaran --}}
                        <div>
                            <x-label for="metode_pembayaran" value="Metode Pembayaran *" class="font-medium" />
                            <select id="metode_pembayaran" wire:model="metode_pembayaran"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500">
                                @foreach ($metodePembayaranOptions as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('metode_pembayaran')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Info Notice --}}
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-3">
                        <div class="flex">
                            <svg class="w-5 h-5 text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <div class="text-sm text-blue-800">
                                <strong>Info:</strong> Transaksi akan langsung diselesaikan. Stok barang akan bertambah
                                dan jurnal akuntansi akan dibuat otomatis.
                            </div>
                        </div>
                    </div>

                    {{-- Product List --}}
                    @if ($id_supplier)
                        <div class="border rounded-lg overflow-hidden">
                            <div class="bg-gray-50 px-4 py-2 border-b">
                                <h4 class="font-medium text-gray-900">Daftar Barang</h4>
                            </div>
                            <div class="overflow-y-auto max-h-96">
                                <table class="w-full">
                                    <thead class="bg-gray-100 sticky top-0">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                                Nama Barang</th>
                                            <th
                                                class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">
                                                Stok</th>
                                            <th
                                                class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">
                                                Harga Beli</th>
                                            <th
                                                class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">
                                                Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @forelse($listBarang as $b)
                                            <tr class="hover:bg-gray-50" wire:key="barang-{{ $b->id }}">
                                                <td class="px-4 py-3">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $b->nama_barang }}</div>
                                                    <div class="text-xs text-gray-500">ID: {{ $b->id }}</div>
                                                </td>
                                                <td class="px-4 py-3 text-center text-sm text-gray-500">
                                                    {{ $b->stok }}</td>
                                                <td class="px-4 py-3 text-center">
                                                    <input type="number"
                                                        wire:model.live="harga_satuan.{{ $b->id }}"
                                                        wire:key="harga-{{ $b->id }}"
                                                        wire:change="updateHarga({{ $b->id }}, $event.target.value)"
                                                        min="0" step="1000"
                                                        class="w-24 text-center text-sm border-gray-300 rounded focus:border-green-500 focus:ring-green-500"
                                                        value="{{ $harga_satuan[$b->id] ?? $b->harga_beli }}" />
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <div class="inline-flex items-center space-x-2">
                                                        <button wire:click="decrease({{ $b->id }})"
                                                            type="button"
                                                            class="w-8 h-8 bg-gray-200 hover:bg-gray-300 rounded-full flex items-center justify-center">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M20 12H4"></path>
                                                            </svg>
                                                        </button>
                                                        <input type="number"
                                                            wire:model.live="quantities.{{ $b->id }}"
                                                            wire:key="qty-{{ $b->id }}" min="0"
                                                            value="{{ $quantities[$b->id] ?? 0 }}"
                                                            class="w-16 text-center text-sm border-gray-300 rounded focus:border-green-500 focus:ring-green-500" />
                                                        <button wire:click="increase({{ $b->id }})"
                                                            type="button"
                                                            class="w-8 h-8 bg-gray-200 hover:bg-gray-300 rounded-full flex items-center justify-center">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                                    Tidak ada barang untuk supplier ini.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @error('quantities')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    @endif
                </div>

                {{-- Right Column: Cart Summary --}}
                <div class="lg:col-span-1">
                    <div class="bg-gray-50 rounded-lg p-4 sticky top-0">
                        <h4 class="font-medium text-gray-900 mb-4">Ringkasan Pembelian</h4>

                        @if ($selectedItems->count() > 0)
                            <div class="space-y-3 mb-4">
                                @foreach ($selectedItems as $item)
                                    <div class="bg-white p-3 rounded border">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <div class="text-sm font-medium">{{ $item['barang']->nama_barang }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $item['quantity'] }} × Rp
                                                    {{ number_format($item['harga'], 0, ',', '.') }}
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-sm font-medium">
                                                    Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                                </div>
                                                <button wire:click="removeFromCart({{ $item['barang']->id }})"
                                                    class="text-red-500 hover:text-red-700 text-xs">
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="border-t pt-4">
                                <div class="flex justify-between items-center text-lg font-bold">
                                    <span>Total:</span>
                                    <span class="text-green-600">Rp
                                        {{ number_format($totalBelanja, 0, ',', '.') }}</span>
                                </div>
                                <div class="text-sm text-gray-500 mt-1">
                                    {{ $selectedItems->count() }} item(s)
                                </div>
                            </div>
                        @else
                            <div class="text-center text-gray-500 py-8">
                                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                                <p>Belum ada barang dipilih</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-between items-center w-full">
                <div class="text-sm text-gray-500">
                    * Field wajib diisi
                </div>
                <div class="flex gap-3">
                    <x-button wire:click="closeModal" class="bg-gray-500 hover:bg-gray-600 text-white">
                        Batal
                    </x-button>
                    <x-button wire:click="store" class="bg-green-600 hover:bg-green-700 text-white" :disabled="$selectedItems->count() === 0">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        Selesaikan Pembelian
                    </x-button>
                </div>
            </div>
        </x-slot>
    </x-dialog-modal>

    {{-- Loading State --}}
    <div wire:loading.delay class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <div class="flex items-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-green-600" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                Menyimpan transaksi pembelian...
            </div>
        </div>
    </div>
</div>
