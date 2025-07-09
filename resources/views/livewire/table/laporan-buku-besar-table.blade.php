<div>
    <!-- Print Styles -->
    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                font-size: 10px !important;
                color: #000 !important;
                background: white !important;
            }

            .print-table {
                border-collapse: collapse !important;
                width: 100% !important;
            }

            .print-table th,
            .print-table td {
                border: 1px solid #000 !important;
                padding: 4px !important;
            }

            .print-table th {
                background: #f0f0f0 !important;
                font-weight: bold !important;
            }

            .text-right {
                text-align: right !important;
            }

            .text-center {
                text-align: center !important;
            }

            .page-break {
                page-break-before: always !important;
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

    <!-- Filter Controls -->
    <div class="no-print mb-6 bg-white dark:bg-gray-800 rounded-xl shadow p-4">
        <h3 class="text-lg font-semibold mb-4">Filter Laporan</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pilih Akun</label>
                <select wire:model.live="selectedAkun"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="all">Semua Akun</option>
                    @foreach ($akunList as $akun)
                        <option value="{{ $akun->id_akun }}">{{ $akun->kode_akun }} - {{ $akun->nama_akun }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Print Header -->
    <div class="print-only" style="display: none;">
        <div style="text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px;">
            <h1 style="font-size: 18px; margin: 0;">LAPORAN BUKU BESAR</h1>
            <p style="font-size: 12px; margin: 5px 0;">
                Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} -
                {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
            </p>
            @if ($selectedAkun !== 'all')
                <p style="font-size: 11px; margin: 5px 0;">
                    Akun: {{ $akunList->find($selectedAkun)->kode_akun }} -
                    {{ $akunList->find($selectedAkun)->nama_akun }}
                </p>
            @endif
        </div>
    </div>

    <!-- Main Content -->
    <div class="space-y-6">
        @if (count($bukuBesarData) > 0)
            @foreach ($bukuBesarData as $akunId => $data)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                    <!-- Account Header -->
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200">
                                    {{ $data['akun']->kode_akun }} - {{ $data['akun']->nama_akun }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $data['akun']->tipe_akun }} | {{ $data['akun']->kategori_akun }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600 dark:text-gray-400">Saldo Awal:</p>
                                <p
                                    class="text-lg font-semibold {{ $data['saldo_awal'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    Rp {{ number_format($data['saldo_awal'], 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Transactions Table -->
                    @if (count($data['transaksi']) > 0)
                        <div class="overflow-x-auto">
                            <table class="print-table min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th
                                            class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <button wire:click="sortBy('tanggal')"
                                                class="no-print hover:text-gray-700 flex items-center gap-1">
                                                Tanggal
                                                @if ($sortBy === 'tanggal')
                                                    <span
                                                        class="text-xs">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                                @endif
                                            </button>
                                            <span class="print-only">Tanggal</span>
                                        </th>
                                        <th
                                            class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Keterangan</th>
                                        <th
                                            class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Debit</th>
                                        <th
                                            class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Kredit</th>
                                        <th
                                            class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Saldo</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800">
                                    @php
                                        $runningSaldo = $data['saldo_awal'];
                                        $tipeAkun = $data['akun']->tipe_akun;
                                    @endphp
                                    @foreach ($data['transaksi'] as $transaksi)
                                        @php
                                            // Hitung saldo berjalan
                                            if (in_array($tipeAkun, ['Aset', 'Beban'])) {
                                                $runningSaldo += $transaksi->debit - $transaksi->kredit;
                                            } else {
                                                $runningSaldo += $transaksi->kredit - $transaksi->debit;
                                            }
                                        @endphp
                                        <tr>
                                            <td class="px-3 py-2 text-sm text-gray-900 dark:text-gray-100">
                                                {{ \Carbon\Carbon::parse($transaksi->tanggal)->format('d/m/Y') }}
                                            </td>
                                            <td class="px-3 py-2 text-sm text-gray-900 dark:text-gray-100">
                                                {{ $transaksi->keterangan }}
                                            </td>
                                            <td class="px-3 py-2 text-sm text-gray-900 dark:text-gray-100 text-right">
                                                @if ($transaksi->debit > 0)
                                                    {{ number_format($transaksi->debit, 0, ',', '.') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="px-3 py-2 text-sm text-gray-900 dark:text-gray-100 text-right">
                                                @if ($transaksi->kredit > 0)
                                                    {{ number_format($transaksi->kredit, 0, ',', '.') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td
                                                class="px-3 py-2 text-sm text-right font-medium {{ $runningSaldo >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                {{ number_format($runningSaldo, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50 dark:bg-gray-700">
                                    <tr class="font-bold">
                                        <td class="px-3 py-2 text-sm text-gray-900 dark:text-gray-100" colspan="2">
                                            Total</td>
                                        <td class="px-3 py-2 text-sm text-blue-600 dark:text-blue-400 text-right">
                                            {{ number_format($data['total_debit'], 0, ',', '.') }}
                                        </td>
                                        <td class="px-3 py-2 text-sm text-blue-600 dark:text-blue-400 text-right">
                                            {{ number_format($data['total_kredit'], 0, ',', '.') }}
                                        </td>
                                        <td
                                            class="px-3 py-2 text-sm text-right font-bold {{ $data['saldo_akhir'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ number_format($data['saldo_akhir'], 0, ',', '.') }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500 dark:text-gray-400">Tidak ada transaksi dalam periode ini</p>
                        </div>
                    @endif

                    <!-- Account Summary -->
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div class="text-center p-2 bg-blue-50 dark:bg-blue-900/20 rounded">
                                <div class="text-blue-600 dark:text-blue-400 font-medium">Saldo Awal</div>
                                <div class="font-bold">{{ number_format($data['saldo_awal'], 0, ',', '.') }}</div>
                            </div>
                            <div class="text-center p-2 bg-green-50 dark:bg-green-900/20 rounded">
                                <div class="text-green-600 dark:text-green-400 font-medium">Total Debit</div>
                                <div class="font-bold">{{ number_format($data['total_debit'], 0, ',', '.') }}</div>
                            </div>
                            <div class="text-center p-2 bg-red-50 dark:bg-red-900/20 rounded">
                                <div class="text-red-600 dark:text-red-400 font-medium">Total Kredit</div>
                                <div class="font-bold">{{ number_format($data['total_kredit'], 0, ',', '.') }}</div>
                            </div>
                            <div
                                class="text-center p-2 {{ $data['saldo_akhir'] >= 0 ? 'bg-emerald-50 dark:bg-emerald-900/20' : 'bg-orange-50 dark:bg-orange-900/20' }} rounded">
                                <div
                                    class="{{ $data['saldo_akhir'] >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-orange-600 dark:text-orange-400' }} font-medium">
                                    Saldo Akhir</div>
                                <div class="font-bold">{{ number_format($data['saldo_akhir'], 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-8 text-center">
                <div class="text-gray-400 mb-4">
                    <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Tidak Ada Data</h3>
                <p class="text-gray-500 dark:text-gray-400">
                    Tidak ada transaksi jurnal yang ditemukan untuk periode dan akun yang dipilih.
                </p>
            </div>
        @endif
    </div>

    <!-- Loading State -->
    <div wire:loading.delay class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 flex items-center gap-4">
            <svg class="animate-spin h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                    stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
            <span class="text-gray-700 dark:text-gray-300">Memuat data...</span>
        </div>
    </div>
</div>
