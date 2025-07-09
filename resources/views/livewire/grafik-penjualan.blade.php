<div>
    {{-- Loading State --}}
    <div wire:loading.delay class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 flex items-center gap-4">
            <svg class="animate-spin h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
            <span class="text-gray-700 dark:text-gray-300">Memperbarui data...</span>
        </div>
    </div>

    <div class="p-6 space-y-6">
        {{-- Header --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-2">Grafik Penjualan</h1>
            <p class="text-gray-600 dark:text-gray-400">Analisis profit dan produk terlaris dengan update real-time</p>
        </div>

        {{-- Summary Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm">Total Profit</p>
                        <p class="text-2xl font-bold">Rp {{ number_format($summaryStats['total_profit'], 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm">Produk Terjual</p>
                        <p class="text-2xl font-bold">{{ number_format($summaryStats['total_produk_terjual']) }} unit
                        </p>
                    </div>
                    <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm">Rata-rata Profit</p>
                        <p class="text-2xl font-bold">Rp {{ number_format($summaryStats['avg_profit'], 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter Controls --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Filter Periode</h3>
            <div class="flex flex-wrap items-center gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rentang Waktu</label>
                    <select wire:model.live="range"
                        class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="harian">Harian</option>
                        <option value="bulanan">Bulanan</option>
                        <option value="tahunan">Tahunan</option>
                        <option value="kustom">Rentang Kustom</option>
                    </select>
                </div>

                @if ($range === 'harian')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tanggal</label>
                        <input type="date" wire:model.live="tanggalSpesifik"
                            class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" />
                    </div>
                @endif

                @if ($range === 'kustom')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tanggal
                            Awal</label>
                        <input type="date" wire:model.live="tanggalAwal"
                            class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tanggal
                            Akhir</label>
                        <input type="date" wire:model.live="tanggalAkhir"
                            class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" />
                    </div>
                @endif

                <div class="flex items-end">
                    <button wire:click="lihatGrafik"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow transition-colors duration-200 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4">
                            </path>
                        </svg>
                        Update Grafik
                    </button>
                </div>
            </div>

            <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <p class="text-sm text-blue-700 dark:text-blue-300">
                    <span class="font-medium">Periode aktif:</span> {{ $summaryStats['periode'] }}
                </p>
            </div>
        </div>

        {{-- Charts --}}
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            {{-- Profit Chart --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Profit Penjualan</h2>
                    <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <span>Laba Bersih</span>
                    </div>
                </div>
                <div class="relative">
                    <canvas id="chartProfit" class="max-h-80"></canvas>
                </div>
            </div>

            {{-- Produk Terlaris Chart --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Produk Terlaris</h2>
                    <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                        <div class="w-3 h-3 bg-purple-500 rounded-full"></div>
                        <span>Jumlah Terjual</span>
                    </div>
                </div>
                <div class="relative">
                    <canvas id="chartProduk" class="max-h-80"></canvas>
                </div>
            </div>
        </div>

        {{-- Data Tables --}}
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            {{-- Profit Data Table --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Detail Profit</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Periode</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Profit</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($profitData as $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $item['label'] }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-green-600 dark:text-green-400">
                                        Rp {{ number_format($item['total_laba'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        Tidak ada data profit untuk periode ini
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Produk Terlaris Data Table --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Detail Produk Terlaris</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Produk</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Terjual</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($produkTerlarisData as $index => $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8">
                                                <div
                                                    class="h-8 w-8 rounded-full bg-purple-100 dark:bg-purple-900 flex items-center justify-center">
                                                    <span
                                                        class="text-xs font-medium text-purple-800 dark:text-purple-200">{{ $index + 1 }}</span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $item['label'] }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-purple-600 dark:text-purple-400">
                                        {{ number_format($item['total_terjual']) }} unit
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        Tidak ada data produk untuk periode ini
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart.js Scripts --}}
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            let chartProfit = null;
            let chartProduk = null;

            function renderChart(canvasId, label, labels, data, color = 'rgba(255,99,132,0.6)', isRupiah = false) {
                const ctx = document.getElementById(canvasId);
                if (!ctx) return;

                // Destroy existing chart
                if (window[canvasId]) {
                    window[canvasId].destroy();
                }

                window[canvasId] = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: label,
                            data: data,
                            backgroundColor: color,
                            borderColor: color.replace('0.6', '1'),
                            borderWidth: 1,
                            borderRadius: 8,
                            borderSkipped: false,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: 'white',
                                bodyColor: 'white',
                                borderColor: color.replace('0.6', '1'),
                                borderWidth: 1,
                                callbacks: {
                                    label: function(context) {
                                        let value = context.parsed.y;
                                        if (isRupiah) {
                                            return `${label}: Rp ${new Intl.NumberFormat('id-ID').format(value)}`;
                                        }
                                        return `${label}: ${value}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.1)',
                                },
                                ticks: {
                                    callback: function(value) {
                                        if (isRupiah) {
                                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                        }
                                        return value;
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false,
                                }
                            }
                        },
                        animation: {
                            duration: 750,
                            easing: 'easeInOutQuart'
                        }
                    }
                });
            }

            function updateCharts() {
                const profitData = @json($profitData);
                const produkData = @json($produkTerlarisData);

                // Profit Chart
                const profitLabels = profitData.map(item => item.label);
                const profitValues = profitData.map(item => item.total_laba);
                renderChart('chartProfit', 'Laba Bersih', profitLabels, profitValues, 'rgba(34, 197, 94, 0.6)', true);

                // Produk Chart
                const produkLabels = produkData.map(item => item.label);
                const produkValues = produkData.map(item => item.total_terjual);
                renderChart('chartProduk', 'Jumlah Terjual', produkLabels, produkValues, 'rgba(147, 51, 234, 0.6)', false);
            }

            // Initialize charts when page loads
            document.addEventListener('DOMContentLoaded', function() {
                updateCharts();
            });

            // Listen to Livewire events for real-time updates
            document.addEventListener('livewire:init', () => {
                Livewire.on('chartDataUpdated', (event) => {
                    updateCharts();
                });
            });

            // Re-render charts when data changes
            Livewire.hook('morph.updated', () => {
                setTimeout(() => {
                    updateCharts();
                }, 100);
            });
        </script>
    @endpush

    {{-- Custom Styles --}}
    @push('styles')
        <style>
            .chart-container {
                position: relative;
                height: 300px;
                width: 100%;
            }

            @media (max-width: 768px) {
                .chart-container {
                    height: 250px;
                }
            }

            /* Loading animation */
            @keyframes pulse {

                0%,
                100% {
                    opacity: 1;
                }

                50% {
                    opacity: .5;
                }
            }

            .pulse {
                animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
            }

            /* Custom scrollbar */
            .overflow-x-auto::-webkit-scrollbar {
                height: 6px;
            }

            .overflow-x-auto::-webkit-scrollbar-track {
                background: #f1f5f9;
                border-radius: 3px;
            }

            .overflow-x-auto::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 3px;
            }

            .overflow-x-auto::-webkit-scrollbar-thumb:hover {
                background: #94a3b8;
            }

            /* Dark mode scrollbar */
            .dark .overflow-x-auto::-webkit-scrollbar-track {
                background: #374151;
            }

            .dark .overflow-x-auto::-webkit-scrollbar-thumb {
                background: #6b7280;
            }

            .dark .overflow-x-auto::-webkit-scrollbar-thumb:hover {
                background: #9ca3af;
            }
        </style>
    @endpush
</div>
