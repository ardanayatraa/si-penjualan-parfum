<div>
    <x-dialog-modal wire:model="open" maxWidth="2xl">
        <x-slot name="title">
            <div class="flex items-center">
                <svg class="w-6 h-6 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1-1H8a1 1 0 00-1 1v3M4 7h16">
                    </path>
                </svg>
                Hapus Transaksi Penjualan
            </div>
        </x-slot>

        <x-slot name="content">
            @if ($transaksi)
                <div class="space-y-6">
                    {{-- Error Messages --}}
                    @error('delete')
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </div>
                        </div>
                    @enderror

                    {{-- Warning Banner --}}
                    @if (!$canDelete)
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <strong>Transaksi tidak dapat dihapus!</strong><br>
                                    Hanya transaksi dengan status "Pending" atau "Dibatalkan" yang dapat dihapus.
                                </div>
                            </div>
                        </div>
                    @elseif($warningMessage)
                        <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-md">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <strong>Peringatan:</strong><br>
                                    {{ $warningMessage }}
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Confirmation Message --}}
                    <div class="text-center">
                        @if ($canDelete)
                            <p class="text-lg text-gray-900 mb-2">
                                Apakah Anda yakin ingin menghapus transaksi ini?
                            </p>
                            <p class="text-sm text-gray-600">
                                Tindakan ini tidak dapat dibatalkan.
                            </p>
                        @else
                            <p class="text-lg text-gray-900 mb-2">
                                Transaksi ini tidak dapat dihapus
                            </p>
                            <p class="text-sm text-gray-600">
                                Silakan ubah status transaksi terlebih dahulu jika diperlukan.
                            </p>
                        @endif
                    </div>

                    {{-- Transaction Details --}}
                    <div class="bg-gray-50 rounded-lg p-4 border">
                        <h4 class="font-medium text-gray-900 mb-3">Detail Transaksi</h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-gray-600">Kode Transaksi:</span>
                                <div>{{ $transaksi->kode_transaksi }}</div>
                            </div>
                            <div>
                                <span class="font-medium text-gray-600">Status:</span>
                                <div>
                                    @php
                                        $statusClass = match ($transaksi->status) {
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'selesai' => 'bg-green-100 text-green-800',
                                            'dibatalkan' => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        };

                                        $statusText = match ($transaksi->status) {
                                            'pending' => 'Pending',
                                            'selesai' => 'Selesai',
                                            'dibatalkan' => 'Dibatalkan',
                                            default => $transaksi->status,
                                        };
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                                        {{ $statusText }}
                                    </span>
                                </div>
                            </div>
                            <div>
                                <span class="font-medium text-gray-600">Tanggal:</span>
                                <div>{{ $transaksi->tanggal_transaksi->format('d-m-Y H:i') }}</div>
                            </div>
                            <div>
                                <span class="font-medium text-gray-600">Kasir:</span>
                                <div>{{ $transaksi->kasir->username ?? 'Unknown' }}</div>
                            </div>
                            <div>
                                <span class="font-medium text-gray-600">Barang:</span>
                                <div>{{ $transaksi->barang->nama_barang ?? 'Unknown' }}</div>
                            </div>
                            <div>
                                <span class="font-medium text-gray-600">Jumlah:</span>
                                <div>{{ $transaksi->jumlah_terjual }} unit</div>
                            </div>
                            <div>
                                <span class="font-medium text-gray-600">Subtotal:</span>
                                <div>Rp {{ number_format($transaksi->subtotal, 0, ',', '.') }}</div>
                            </div>
                            <div>
                                <span class="font-medium text-gray-600">Total Harga:</span>
                                <div class="font-semibold">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}
                                </div>
                            </div>
                            <div>
                                <span class="font-medium text-gray-600">Metode Pembayaran:</span>
                                <div>
                                    @php
                                        $metodePembayaran = [
                                            'cash' => 'Tunai',
                                            'transfer' => 'Transfer Bank',
                                            'debit_card' => 'Kartu Debit',
                                            'credit_card' => 'Kartu Kredit',
                                            'e_wallet' => 'E-Wallet',
                                        ];
                                    @endphp
                                    {{ $metodePembayaran[$transaksi->metode_pembayaran] ?? $transaksi->metode_pembayaran }}
                                </div>
                            </div>
                            <div>
                                <span class="font-medium text-gray-600">Laba Bruto:</span>
                                <div class="text-green-600 font-medium">Rp
                                    {{ number_format($transaksi->laba_bruto, 0, ',', '.') }}</div>
                            </div>
                        </div>

                        {{-- Pajak Info --}}
                        @if ($transaksi->pajak)
                            <div class="mt-3 pt-3 border-t">
                                <div class="text-sm">
                                    <span class="font-medium text-gray-600">Pajak:</span>
                                    {{ $transaksi->pajak->presentase }}%
                                    (Rp
                                    {{ number_format(($transaksi->subtotal * $transaksi->pajak->presentase) / 100, 0, ',', '.') }})
                                </div>
                            </div>
                        @endif

                        {{-- Piutang Info --}}
                        @if ($transaksi->piutang)
                            <div class="mt-3 pt-3 border-t">
                                <div class="text-sm text-orange-600">
                                    <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <strong>Perhatian:</strong> Transaksi ini memiliki piutang sebesar
                                    Rp {{ number_format($transaksi->piutang->jumlah, 0, ',', '.') }} yang akan ikut
                                    terhapus.
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-end gap-3">
                <x-button wire:click="closeModal" class="bg-gray-500 hover:bg-gray-600 text-white">
                    Batal
                </x-button>

                @if ($canDelete)
                    <x-button wire:click="delete" class="bg-red-600 hover:bg-red-700 text-white">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1-1H8a1 1 0 00-1 1v3M4 7h16">
                            </path>
                        </svg>
                        Ya, Hapus Transaksi
                    </x-button>
                @endif
            </div>
        </x-slot>
    </x-dialog-modal>

    {{-- Loading State --}}
    <div wire:loading.delay class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <div class="flex items-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-red-600" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                Menghapus transaksi...
            </div>
        </div>
    </div>
</div>
