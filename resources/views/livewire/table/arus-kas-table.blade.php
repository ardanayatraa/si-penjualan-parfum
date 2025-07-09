<div>
    <!-- Print Styles -->
    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                font-size: 12px !important;
                color: #000 !important;
                background: white !important;
            }

            .print-table {
                border-collapse: collapse !important;
            }

            .print-table th,
            .print-table td {
                border: 1px solid #000 !important;
                padding: 8px !important;
            }

            .print-table th {
                background: #f0f0f0 !important;
                font-weight: bold !important;
            }

            .text-right {
                text-align: right !important;
            }
        }
    </style>

    <!-- Action Buttons -->
    <div class="no-print mb-4 flex gap-2">


        <button wire:click="exportPdf"
            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                </path>
            </svg>
            Export PDF
        </button>
    </div>

    <!-- Date Range Filter -->
    <div class="no-print mb-6 bg-white dark:bg-gray-800 rounded-xl shadow p-4">
        <h3 class="text-lg font-semibold mb-4">Filter Periode</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tanggal Mulai</label>
                <input type="date" wire:model.live="startDate"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tanggal Akhir</label>
                <input type="date" wire:model.live="endDate"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
    </div>

    <!-- Print Header (hidden on screen) -->
    <div class="print-only" style="display: none;">
        <div style="text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px;">
            <h1 style="font-size: 18px; margin: 0;">LAPORAN ARUS KAS</h1>
            <p style="font-size: 12px; margin: 5px 0;">
                Periode: {{ \Carbon\Carbon::parse($startDate ?? now()->startOfMonth())->format('d/m/Y') }} -
                {{ \Carbon\Carbon::parse($endDate ?? now())->format('d/m/Y') }}
            </p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Arus Kas Masuk -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
            <h2 class="text-lg font-bold mb-2 text-green-700">Arus Kas Masuk</h2>
            <table class="print-table min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Keterangan</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Jumlah (Rp)</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800">
                    @foreach ($kasMasuk as $item)
                        <tr>
                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $item['keterangan'] }}
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100 text-right">
                                {{ number_format($item['jumlah'], 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 dark:bg-gray-700">
                    <tr class="font-bold">
                        <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">Total Kas Masuk</td>
                        <td class="px-4 py-2 text-sm text-green-700 dark:text-green-400 text-right">
                            {{ number_format($totals['totalKasMasuk'], 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Arus Kas Keluar -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
            <h2 class="text-lg font-bold mb-2 text-red-700">Arus Kas Keluar</h2>
            <table class="print-table min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Keterangan</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Jumlah (Rp)</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800">
                    @foreach ($kasKeluar as $item)
                        <tr>
                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $item['keterangan'] }}
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100 text-right">
                                {{ number_format($item['jumlah'], 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 dark:bg-gray-700">
                    <tr class="font-bold">
                        <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">Total Kas Keluar</td>
                        <td class="px-4 py-2 text-sm text-red-700 dark:text-red-400 text-right">
                            {{ number_format($totals['totalKasKeluar'], 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Summary Section -->
    <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow p-4">
        <h2 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-200">Ringkasan Arus Kas</h2>

        <!-- Print Summary -->
        <div class="print-only" style="display: none;">
            <div style="border: 2px solid #000; padding: 15px; background: #f9f9f9;">
                <div
                    style="display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px solid #ccc;">
                    <span>Total Kas Masuk:</span>
                    <span style="font-weight: bold;">Rp
                        {{ number_format($totals['totalKasMasuk'], 0, ',', '.') }}</span>
                </div>
                <div
                    style="display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px solid #ccc;">
                    <span>Total Kas Keluar:</span>
                    <span style="font-weight: bold;">Rp
                        {{ number_format($totals['totalKasKeluar'], 0, ',', '.') }}</span>
                </div>
                <div
                    style="display: flex; justify-content: space-between; padding: 15px 0 5px 0; border-bottom: 2px solid #000; font-weight: bold; font-size: 14px;">
                    <span>Arus Kas Bersih:</span>
                    <span>Rp {{ number_format($totals['arusKasBersih'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Screen Summary -->
        <div class="no-print grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                <h3 class="text-sm font-medium text-green-600 dark:text-green-400">Total Kas Masuk</h3>
                <p class="text-2xl font-bold text-green-700 dark:text-green-300">
                    Rp {{ number_format($totals['totalKasMasuk'], 0, ',', '.') }}
                </p>
            </div>

            <div class="text-center p-4 bg-red-50 dark:bg-red-900/20 rounded-lg">
                <h3 class="text-sm font-medium text-red-600 dark:text-red-400">Total Kas Keluar</h3>
                <p class="text-2xl font-bold text-red-700 dark:text-red-300">
                    Rp {{ number_format($totals['totalKasKeluar'], 0, ',', '.') }}
                </p>
            </div>

            <div
                class="text-center p-4 {{ $totals['arusKasBersih'] >= 0 ? 'bg-blue-50 dark:bg-blue-900/20' : 'bg-orange-50 dark:bg-orange-900/20' }} rounded-lg">
                <h3
                    class="text-sm font-medium {{ $totals['arusKasBersih'] >= 0 ? 'text-blue-600 dark:text-blue-400' : 'text-orange-600 dark:text-orange-400' }}">
                    Arus Kas Bersih
                </h3>
                <p
                    class="text-2xl font-bold {{ $totals['arusKasBersih'] >= 0 ? 'text-blue-700 dark:text-blue-300' : 'text-orange-700 dark:text-orange-300' }}">
                    Rp {{ number_format($totals['arusKasBersih'], 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>

    <script>
        @if (session()->has('pdf-generated'))
            // Optional: Show success message when PDF is generated
            setTimeout(() => {
                alert('PDF berhasil didownload!');
            }, 100);
        @endif
    </script>
</div>
