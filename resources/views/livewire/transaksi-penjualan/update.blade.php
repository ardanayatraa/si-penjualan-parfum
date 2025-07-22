<div>
    <x-dialog-modal wire:model="open">
        <x-slot name="title">
            <div class="flex items-center">
                <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                    </path>
                </svg>
                Edit Transaksi Penjualan
            </div>
        </x-slot>

        <x-slot name="content">
            <div class="flex flex-col gap-6">
                {{-- Error General --}}
                @error('general')
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md">
                        {{ $message }}
                    </div>
                @enderror

                {{-- Info Notice --}}
                <div class="bg-indigo-50 border border-indigo-200 rounded-md p-3">
                    <div class="flex">
                        <svg class="w-5 h-5 text-indigo-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <div class="text-sm text-indigo-800">
                            <strong>Info:</strong> Transaksi akan langsung diselesaikan setelah update. Stok barang lama
                            akan dikembalikan, stok barang baru akan berkurang, dan jurnal akuntansi akan diperbarui.
                        </div>
                    </div>
                </div>

                {{-- Pilih Barang --}}
                <div>
                    <x-label for="id_barang" value="Barang *" class="font-medium" />
                    <select id="id_barang" wire:model.live="id_barang"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">-- Pilih Barang --</option>
                        @foreach ($listBarang as $b)
                            <option value="{{ $b->id }}">
                                {{ $b->nama_barang }}
                                @if ($b->id !== $id_barang)
                                    (Stok: {{ $b->stok }})
                                @else
                                    (Stok: {{ $b->stok + ($originalData['jumlah_terjual'] ?? 0) }})
                                @endif
                                - Rp {{ number_format($b->harga_jual, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_barang')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror

                    {{-- Info Barang Terpilih --}}
                    @if ($selectedBarang)
                        <div class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-md">
                            <div class="text-sm text-blue-800">
                                <div><strong>Barang:</strong> {{ $selectedBarang->nama_barang }}</div>
                                <div><strong>Stok Tersedia:</strong>
                                    @if ($selectedBarang->id === ($originalData['id_barang'] ?? null))
                                        {{ $selectedBarang->stok + ($originalData['jumlah_terjual'] ?? 0) }} unit
                                        <span class="text-xs text-gray-600">(termasuk yang sedang digunakan)</span>
                                    @else
                                        {{ $selectedBarang->stok }} unit
                                    @endif
                                </div>
                                <div><strong>Harga Jual:</strong> Rp
                                    {{ number_format($selectedBarang->harga_jual, 0, ',', '.') }}</div>
                                <div><strong>Harga Pokok:</strong> Rp
                                    {{ number_format($selectedBarang->harga_beli, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Jumlah Terjual --}}
                <div>
                    <x-label for="jumlah_terjual" value="Jumlah Terjual *" class="font-medium" />
                    <x-input id="jumlah_terjual" type="number" wire:model.live="jumlah_terjual" min="1"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Masukkan jumlah..." />
                    @error('jumlah_terjual')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Row: Tanggal & Metode Pembayaran --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Tanggal Transaksi --}}
                    <div>
                        <x-label for="tanggal_transaksi" value="Tanggal Transaksi *" class="font-medium" />
                        <x-input id="tanggal_transaksi" type="date" wire:model.live="tanggal_transaksi"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        @error('tanggal_transaksi')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Metode Pembayaran --}}
                    <div>
                        <x-label for="metode_pembayaran" value="Metode Pembayaran *" class="font-medium" />
                        <select id="metode_pembayaran" wire:model="metode_pembayaran"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach ($metodePembayaranOptions as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('metode_pembayaran')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Pajak --}}
                <div>
                    <x-label for="id_pajak" value="Pajak" class="font-medium" />
                    <select id="id_pajak" wire:model.live="id_pajak"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">-- Tanpa Pajak --</option>
                        @foreach ($listPajak as $p)
                            <option value="{{ $p->id }}">{{ $p->presentase }}% Pajak</option>
                        @endforeach
                    </select>
                </div>

                {{-- Hasil Perhitungan --}}
                @if ($subtotal > 0)
                    <div class="bg-gray-50 p-4 rounded-lg border">
                        <h4 class="font-medium text-gray-900 mb-3">Rincian Transaksi</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span>Subtotal ({{ $jumlah_terjual }} × Rp
                                    {{ number_format($harga_jual, 0, ',', '.') }})</span>
                                <span class="font-medium">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>

                            @if ($pajak_amount > 0)
                                <div class="flex justify-between text-orange-600">
                                    <span>Pajak</span>
                                    <span>Rp {{ number_format($pajak_amount, 0, ',', '.') }}</span>
                                </div>
                            @endif

                            <hr class="my-2">

                            <div class="flex justify-between text-lg font-bold text-indigo-600">
                                <span>Total Harga</span>
                                <span>Rp {{ number_format($total_harga, 0, ',', '.') }}</span>
                            </div>

                            @if ($laba_bruto > 0)
                                <div class="flex justify-between text-green-600">
                                    <span>Laba Bruto</span>
                                    <span>Rp {{ number_format($laba_bruto, 0, ',', '.') }}</span>
                                </div>
                            @endif

                            <div class="flex justify-between text-blue-600">
                                <span>Metode Pembayaran</span>
                                <span>{{ $metodePembayaranOptions[$metode_pembayaran] ?? $metode_pembayaran }}</span>
                            </div>
                        </div>
                    </div>
                @endif
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
                    <x-button wire:click="update" class="bg-indigo-600 hover:bg-indigo-700 text-white"
                        :disabled="!$subtotal">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                        Update & Selesaikan
                    </x-button>
                </div>
            </div>
        </x-slot>
    </x-dialog-modal>

    {{-- Loading State --}}
    <div wire:loading.delay class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <div class="flex items-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                Mengupdate transaksi penjualan...
            </div>
        </div>
    </div>
</div>
