<!-- resources/views/livewire/return-barang/update.blade.php -->
<div>
    <x-dialog-modal wire:model="open" maxWidth="2xl">
        <x-slot name="title">
            <div class="flex items-center">
                <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                    </path>
                </svg>
                Edit Return Barang
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

                {{-- Warning untuk perubahan stok --}}
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-md">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span class="font-medium">Peringatan:</span> Perubahan return akan mempengaruhi stok barang dan
                        jurnal keuangan.
                    </div>
                </div>

                {{-- Pilih Barang --}}
                <div>
                    <x-label for="id_barang" value="Barang *" class="font-medium" />
                    <select id="id_barang" wire:model.live="id_barang"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">
                        <option value="">-- Pilih Barang --</option>
                        @foreach ($listBarang as $b)
                            <option value="{{ $b->id }}">
                                {{ $b->nama_barang }} - {{ $b->supplier->nama_supplier ?? 'No Supplier' }}
                                @if ($b->id == $id_barang)
                                    (Stok tersedia: {{ $b->stok + $originalJumlah }})
                                @else
                                    (Stok: {{ $b->stok }})
                                @endif
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
                                <div><strong>Stok Tersedia untuk Return:</strong> {{ $availableStok }} unit</div>
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
                            max="{{ $availableStok }}"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500"
                            placeholder="Masukkan jumlah..." />
                        @error('jumlah')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                        @if ($availableStok > 0)
                            <div class="text-xs text-gray-500 mt-1">
                                Maksimal: {{ $availableStok }} unit
                                @if ($originalJumlah > 0)
                                    (termasuk {{ $originalJumlah }} dari return sebelumnya)
                                @endif
                            </div>
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

                {{-- Comparison: Before vs After --}}
                @if ($selectedBarang && $jumlah > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Before --}}
                        <div class="bg-gray-50 p-4 rounded-lg border">
                            <h4 class="font-medium text-gray-900 mb-3">Data Sebelumnya</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span>Jumlah Return:</span>
                                    <span>{{ $originalJumlah }} unit</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Nilai Return:</span>
                                    <span>Rp
                                        {{ number_format($selectedBarang->harga_beli * $originalJumlah, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- After --}}
                        <div class="bg-orange-50 p-4 rounded-lg border border-orange-200">
                            <h4 class="font-medium text-gray-900 mb-3">Data Baru</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span>Jumlah Return:</span>
                                    <span class="font-medium">{{ $jumlah }} unit</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Nilai Return:</span>
                                    <span class="font-medium text-orange-600">Rp
                                        {{ number_format($selectedBarang->harga_beli * $jumlah, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Selisih:</span>
                                    <span
                                        class="font-medium {{ $jumlah - $originalJumlah > 0 ? 'text-red-600' : 'text-green-600' }}">
                                        {{ $jumlah - $originalJumlah > 0 ? '+' : '' }}{{ $jumlah - $originalJumlah }}
                                        unit
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Informasi Tambahan --}}
                <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                    <h4 class="font-medium text-blue-900 mb-2">Dampak Perubahan:</h4>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li>• Stok barang akan disesuaikan dengan perubahan jumlah return</li>
                        <li>• Jurnal lama akan dihapus dan diganti dengan jurnal baru</li>
                        <li>• Jika ganti barang, stok barang lama akan dikembalikan</li>
                        <li>• Nilai return akan dihitung berdasarkan harga beli saat ini</li>
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
                    <x-button wire:click="update" class="bg-blue-600 hover:bg-blue-700 text-white" :disabled="!$selectedBarang || !$jumlah || !$alasan">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                        Update Return
                    </x-button>
                </div>
            </div>
        </x-slot>
    </x-dialog-modal>

    {{-- Loading State --}}
    <div wire:loading.delay class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <div class="flex items-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                Mengupdate return barang...
            </div>
        </div>
    </div>
</div>
