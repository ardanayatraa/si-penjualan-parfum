<div>
    <!-- Modal -->
    <div x-data="{ open: @entangle('open') }" x-show="open" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="$wire.closeModal()"></div>

            <!-- Modal panel -->
            <div x-show="open" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block w-full max-w-2xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">

                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-medium text-gray-900">
                        Tambah Return Barang
                    </h3>
                    <button type="button" wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="store">
                    <div class="grid grid-cols-1 gap-6">

                        <!-- Pilih Barang -->
                        <div>
                            <label for="id_barang" class="block text-sm font-medium text-gray-700 mb-2">
                                Pilih Barang <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.live="id_barang" id="id_barang"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Pilih Barang --</option>
                                @foreach ($listBarang as $barang)
                                    <option value="{{ $barang->id }}">
                                        {{ $barang->nama_barang }} -
                                        {{ $barang->supplier->nama_supplier ?? 'Tanpa Supplier' }} (Stok:
                                        {{ $barang->stok }})
                                    </option>
                                @endforeach
                            </select>
                            @error('id_barang')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Info Barang Terpilih -->
                        @if ($selectedBarang)
                            <div class="p-4 bg-blue-50 rounded-lg">
                                <h4 class="font-medium text-blue-900">Informasi Barang</h4>
                                <div class="grid grid-cols-2 gap-4 mt-2 text-sm">
                                    <div>
                                        <span class="text-gray-600">Nama:</span>
                                        <span class="font-medium">{{ $selectedBarang->nama_barang }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Supplier:</span>
                                        <span
                                            class="font-medium">{{ $selectedBarang->supplier->nama_supplier ?? '-' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Stok Tersedia:</span>
                                        <span class="font-medium text-green-600">{{ $selectedBarang->stok }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Harga Beli:</span>
                                        <span class="font-medium">Rp
                                            {{ number_format($selectedBarang->harga_beli, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Jumlah Return -->
                        <div>
                            <label for="jumlah" class="block text-sm font-medium text-gray-700 mb-2">
                                Jumlah Return <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" wire:model.live="jumlah" id="jumlah" min="1"
                                    max="{{ $availableStok }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Masukkan jumlah return">
                                @if ($availableStok > 0)
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <span class="text-sm text-gray-500">/ {{ $availableStok }}</span>
                                    </div>
                                @endif
                            </div>
                            @error('jumlah')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Alasan Return -->
                        <div>
                            <label for="alasan" class="block text-sm font-medium text-gray-700 mb-2">
                                Alasan Return <span class="text-red-500">*</span>
                            </label>
                            <textarea wire:model="alasan" id="alasan" rows="3" maxlength="500"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Masukkan alasan return barang..."></textarea>
                            <div class="flex justify-between mt-1">
                                @error('alasan')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                @else
                                    <p class="text-sm text-gray-500">Alasan return wajib diisi</p>
                                @enderror
                                <p class="text-sm text-gray-400">{{ strlen($alasan ?? '') }}/500</p>
                            </div>
                        </div>

                        <!-- Tanggal Return -->
                        <div>
                            <label for="tanggal_return" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Return <span class="text-red-500">*</span>
                            </label>
                            <input type="date" wire:model="tanggal_return" id="tanggal_return"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('tanggal_return')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Total Nilai Return (Preview) -->
                        @if ($selectedBarang && $jumlah)
                            <div class="p-4 bg-yellow-50 rounded-lg">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700 font-medium">Total Nilai Return:</span>
                                    <span class="text-lg font-bold text-yellow-800">
                                        Rp {{ number_format($selectedBarang->harga_beli * $jumlah, 0, ',', '.') }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">
                                    {{ $jumlah }} unit × Rp
                                    {{ number_format($selectedBarang->harga_beli, 0, ',', '.') }}
                                </p>
                            </div>
                        @endif

                        <!-- Error General -->
                        @error('general')
                            <div class="p-3 text-sm text-red-700 bg-red-100 rounded-lg">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                        <button type="button" wire:click="closeModal"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <span wire:loading.remove wire:target="store">Simpan Return</span>
                            <span wire:loading wire:target="store" class="flex items-center">
                                <svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Menyimpan...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
