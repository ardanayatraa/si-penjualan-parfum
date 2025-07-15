<div>
    <div>
        <x-button wire:click="$set('open', true)" class="bg-indigo-600 hover:bg-indigo-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Transaksi Penjualan
        </x-button>

        <x-dialog-modal wire:model="open">
            <x-slot name="title">
                <div class="flex items-center">
                    <svg class="w-6 h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    Tambah Transaksi Penjualan
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

                    {{-- Pilih Barang --}}
                    <div>
                        <x-label for="id_barang" value="Barang *" class="font-medium" />
                        <select id="id_barang" wire:model.live="id_barang"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">-- Pilih Barang --</option>
                            @foreach ($listBarang as $b)
                                <option value="{{ $b->id }}">
                                    {{ $b->nama_barang }} (Stok: {{ $b->stok }}) - Rp
                                    {{ number_format($b->harga_jual, 0, ',', '.') }}
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
                                    <div><strong>Stok Tersedia:</strong> {{ $selectedBarang->stok }} unit</div>
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
                            max="{{ $selectedBarang?->stok ?? 999 }}"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Masukkan jumlah..." />
                        @error('jumlah_terjual')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                        @if ($selectedBarang && $selectedBarang->stok > 0)
                            <div class="text-xs text-gray-500 mt-1">Maksimal: {{ $selectedBarang->stok }} unit</div>
                        @endif
                    </div>

                    {{-- Row: Tanggal & Metode Pembayaran --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Tanggal Transaksi --}}
                        <div>
                            <x-label for="tanggal_transaksi" value="Tanggal Transaksi *" class="font-medium" />
                            <x-input id="tanggal_transaksi" type="date" wire:model="tanggal_transaksi"
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

                    {{-- Status Transaksi --}}
                    <div>
                        <x-label value="Status Transaksi *" class="font-medium" />
                        <div class="flex gap-4 mt-2">
                            <label class="inline-flex items-center">
                                <input type="radio" wire:model.live="status" value="pending"
                                    class="form-radio text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-2 text-sm">Draft (Pending)</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" wire:model.live="status" value="selesai"
                                    class="form-radio text-green-600 focus:ring-green-500">
                                <span class="ml-2 text-sm">Selesaikan Transaksi</span>
                            </label>
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            @if ($status === 'pending')
                                Draft: Transaksi disimpan tanpa mengurangi stok
                            @else
                                Selesai: Stok barang akan dikurangi dan jurnal dibuat
                            @endif
                        </div>
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
                        <x-button wire:click="store" class="bg-blue-600 hover:bg-blue-700 text-white"
                            :disabled="!$subtotal">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ $status === 'selesai' ? 'Simpan & Selesaikan' : 'Simpan Draft' }}
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
                    Menyimpan transaksi...
                </div>
            </div>
        </div>
    </div>

    {{-- Toast Notification (jika menggunakan AlpineJS) --}}
    <div x-data="{
        show: false,
        message: '',
        type: 'success'
    }"
        x-on:show-toast.window="
        show = true;
        message = $event.detail.message;
        type = $event.detail.type;
        setTimeout(() => show = false, 3000)
     "
        x-show="show" x-transition class="fixed top-4 right-4 z-50" style="display: none;">
        <div :class="{
            'bg-green-500': type === 'success',
            'bg-red-500': type === 'error',
            'bg-yellow-500': type === 'warning'
        }"
            class="text-white px-6 py-3 rounded-lg shadow-lg">
            <span x-text="message"></span>
        </div>
    </div>
</div>
