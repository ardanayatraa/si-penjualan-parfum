<!-- resources/views/livewire/return-barang/create.blade.php -->
<div>
    <x-button wire:click="$set('open', true)" class="bg-orange-600 hover:bg-orange-700">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
        </svg>
        Tambah Return Barang
    </x-button>

    <x-dialog-modal wire:model="open" maxWidth="2xl">
        <x-slot name="title">
            <div class="flex items-center">
                <svg class="w-6 h-6 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                </svg>
                Tambah Return Barang
            </div>
        </x-slot>

        <x-slot name="content">
            <div class="space-y-6">
                {{-- Error General --}}
                @error('general')
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md">
                        {{ $message }}
                    </div>
                @enderror

                {{-- Pilih Barang --}}
                <div>
                    <x-label for="id_barang" value="Barang *" class="font-medium" />
                    <select id="id_barang" wire:model.live="id_barang"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">
                        <option value="">-- Pilih Barang --</option>
                        @foreach ($listBarang as $b)
                            <option value="{{ $b->id }}">
                                {{ $b->nama_barang }} (Stok: {{ $b->stok }}) -
                                {{ $b->supplier->nama_supplier ?? 'No Supplier' }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_barang')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror

                    {{-- Info Barang Terpilih --}}
                    @if ($selectedBarang)
                        <div class="mt-2 p-3 bg-orange-50 border border-orange-200 rounded-md">
                            <div class="text-sm text-orange-800">
                                <div><strong>Barang:</strong> {{ $selectedBarang->nama_barang }}</div>
                                <div><strong>Supplier:</strong>
                                    {{ $selectedBarang->supplier->nama_supplier ?? 'Tidak ada supplier' }}</div>
                                <div><strong>Stok Saat Ini:</strong> {{ $selectedBarang->stok }} unit</div>
                                <div><strong>Harga Beli:</strong> Rp
                                    {{ number_format($selectedBarang->harga_beli, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Jumlah dan Tanggal --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Jumlah --}}
                    <div>
                        <x-label for="jumlah" value="Jumlah Return *" class="font-medium" />
                        <x-input id="jumlah" type="number" wire:model.live="jumlah" min="1"
                            max="{{ $selectedBarang?->stok ?? 999 }}"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500"
                            placeholder="Masukkan jumlah..." />
                        @error('jumlah')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                        @if ($selectedBarang)
                            <div class="text-xs text-gray-500 mt-1">Maksimal: {{ $selectedBarang->stok }} unit</div>
                        @endif
                    </div>

                    {{-- Tanggal Return --}}
                    <div>
                        <x-label for="tanggal_return" value="Tanggal Return *" class="font-medium" />
                        <x-input id="tanggal_return" type="date" wire:model="tanggal_return"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500" />
                        @error('tanggal_return')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Alasan Return --}}
                <div>
                    <x-label for="alasan" value="Alasan Return *" class="font-medium" />
                    <textarea id="alasan" wire:model="alasan" rows="4"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500"
                        placeholder="Jelaskan alasan return barang..."></textarea>
                    @error('alasan')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                    <div class="text-xs text-gray-500 mt-1">Maksimal 500 karakter</div>
                </div>

                {{-- Ringkasan Return --}}
                @if ($selectedBarang && $jumlah > 0)
                    <div class="bg-gray-50 p-4 rounded-lg border">
                        <h4 class="font-medium text-gray-900 mb-3">Ringkasan Return</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span>Barang:</span>
                                <span class="font-medium">{{ $selectedBarang->nama_barang }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Supplier:</span>
                                <span
                                    class="font-medium">{{ $selectedBarang->supplier->nama_supplier ?? 'Tidak ada supplier' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Jumlah Return:</span>
                                <span class="font-medium">{{ $jumlah }} unit</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Harga per Unit:</span>
                                <span class="font-medium">Rp
                                    {{ number_format($selectedBarang->harga_beli, 0, ',', '.') }}</span>
                            </div>
                            <hr class="my-2">
                            <div class="flex justify-between text-lg font-bold text-orange-600">
                                <span>Total Nilai Return:</span>
                                <span>Rp {{ number_format($selectedBarang->harga_beli * $jumlah, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Informasi Tambahan --}}
                <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                    <h4 class="font-medium text-blue-900 mb-2">Informasi Penting:</h4>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li>• Stok barang akan berkurang sesuai jumlah return</li>
                        <li>• Jurnal akuntansi akan otomatis dibuat</li>
                        <li>• Pastikan alasan return jelas dan detail</li>
                        <li>• Return ini akan dicatat sebagai pengurang hutang dagang</li>
                    </ul>
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
                    <x-button wire:click="store" class="bg-orange-600 hover:bg-orange-700 text-white" :disabled="!$selectedBarang || !$jumlah || !$alasan">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        Simpan Return
                    </x-button>
                </div>
            </div>
        </x-slot>
    </x-dialog-modal>

    {{-- Loading State --}}
    <div wire:loading.delay class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <div class="flex items-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-orange-600" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                        
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                Menyimpan return barang...
            </div>
        </div>
    </div>
</div>
