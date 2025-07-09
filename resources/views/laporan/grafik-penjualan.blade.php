<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        {{-- Header --}}
        <div class="bg-white dark:bg-gray-800 shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                            Dashboard Grafik Penjualan
                        </h1>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Analisis profit dan produk terlaris
                        </p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button id="btn-export"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4">
                                </path>
                            </svg>
                            Export
                        </button>
                        <button id="btn-refresh"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                </path>
                            </svg>
                            Refresh
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto p-6 space-y-6">
            {{-- Summary Stats with animation --}}
            <div id="summary-stats" class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div
                    class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-6 text-white transform transition-transform hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm">Total Profit</p>
                            <p id="total-profit" class="text-2xl font-bold">Rp 0</p>
                            <p id="profit-change" class="text-xs text-blue-200 mt-1"></p>
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

                <div
                    class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-6 text-white transform transition-transform hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm">Total Penjualan</p>
                            <p id="total-penjualan" class="text-2xl font-bold">Rp 0</p>
                            <p id="penjualan-change" class="text-xs text-green-200 mt-1"></p>
                        </div>
                        <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-6 text-white transform transition-transform hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm">Total Transaksi</p>
                            <p id="total-transaksi" class="text-2xl font-bold">0</p>
                            <p id="transaksi-change" class="text-xs text-purple-200 mt-1"></p>
                        </div>
                        <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl p-6 text-white transform transition-transform hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-orange-100 text-sm">Produk Terjual</p>
                            <p id="total-produk" class="text-2xl font-bold">0 unit</p>
                            <p id="produk-change" class="text-xs text-orange-200 mt-1"></p>
                        </div>
                        <div class="bg-orange-400 bg-opacity-30 rounded-full p-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
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
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rentang
                            Waktu</label>
                        <select id="filter-range"
                            class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="harian">Harian</option>
                            <option value="bulanan">Bulanan</option>
                            <option value="tahunan">Tahunan</option>
                            <option value="kustom">Rentang Kustom</option>
                        </select>
                    </div>

                    <div id="tanggal-spesifik-container">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tanggal</label>
                        <input type="date" id="tanggal-spesifik" value="{{ date('Y-m-d') }}"
                            class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" />
                    </div>

                    <div id="tanggal-awal-container" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tanggal
                            Awal</label>
                        <input type="date" id="tanggal-awal" value="{{ date('Y-m-01') }}"
                            class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" />
                    </div>

                    <div id="tanggal-akhir-container" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tanggal
                            Akhir</label>
                        <input type="date" id="tanggal-akhir" value="{{ date('Y-m-d') }}"
                            class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" />
                    </div>

                    <div class="flex items-end gap-2">
                        <button id="btn-lihat"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow transition-colors duration-200 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4">
                                </path>
                            </svg>
                            Update Grafik
                        </button>

                        <div class="flex items-center gap-2">
                            <button id="btn-chart-type"
                                class="p-2 text-gray-500 hover:text-gray-700 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                    </path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div id="periode-info" class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <p class="text-sm text-blue-700 dark:text-blue-300">
                        <span class="font-medium">Periode aktif:</span> <span id="periode-text">Hari ini</span>
                    </p>
                </div>
            </div>

            {{-- Charts --}}
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                {{-- Profit Chart --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Profit Penjualan</h2>
                        <div class="flex items-center gap-2">
                            <button class="chart-zoom-btn p-1 text-gray-400 hover:text-gray-600" data-chart="profit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7">
                                    </path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div id="chartProfit" class="relative h-80"></div>
                </div>

                {{-- Produk Terlaris Chart --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Produk Terlaris</h2>
                        <div class="flex items-center gap-2">
                            <button class="chart-zoom-btn p-1 text-gray-400 hover:text-gray-600" data-chart="produk">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7">
                                    </path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div id="chartProduk" class="relative h-80"></div>
                </div>
            </div>

            {{-- Additional Charts Row --}}
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                {{-- Trend Chart --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Trend Penjualan</h2>
                    </div>
                    <div id="chartTrend" class="relative h-80"></div>
                </div>

                {{-- Comparison Chart --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Perbandingan Kategori</h2>
                    </div>
                    <div id="chartComparison" class="relative h-80"></div>
                </div>
            </div>
        </div>

        {{-- Loading Overlay --}}
        <div id="loading-overlay"
            class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden items-center justify-center z-50">
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

    {{-- ApexCharts CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@latest"></script>

    <script>
        // Global variables untuk charts
        let chartProfit = null;
        let chartProduk = null;
        let chartTrend = null;
        let chartComparison = null;
        let currentChartType = 'bar';

        // Utility functions
        function formatRupiah(number) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(number);
        }

        function showLoading() {
            document.getElementById('loading-overlay').classList.remove('hidden');
            document.getElementById('loading-overlay').classList.add('flex');
        }

        function hideLoading() {
            document.getElementById('loading-overlay').classList.add('hidden');
            document.getElementById('loading-overlay').classList.remove('flex');
        }

        // ApexCharts theme configuration
        const isDarkMode = document.documentElement.classList.contains('dark');
        const chartTheme = {
            mode: isDarkMode ? 'dark' : 'light',
            palette: 'palette1',
            monochrome: {
                enabled: false,
                color: '#255aee',
                shadeTo: 'light',
                shadeIntensity: 0.65
            }
        };

        // Create Profit Chart
        function createProfitChart(labels, data) {
            const options = {
                series: [{
                    name: 'Laba Bersih',
                    data: data
                }],
                chart: {
                    type: currentChartType,
                    height: 320,
                    toolbar: {
                        show: true,
                        tools: {
                            download: true,
                            selection: true,
                            zoom: true,
                            zoomin: true,
                            zoomout: true,
                            pan: true,
                            reset: true
                        }
                    },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800,
                        animateGradually: {
                            enabled: true,
                            delay: 150
                        },
                        dynamicAnimation: {
                            enabled: true,
                            speed: 350
                        }
                    }
                },
                theme: chartTheme,
                plotOptions: {
                    bar: {
                        borderRadius: 8,
                        dataLabels: {
                            position: 'top'
                        }
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return formatRupiah(val);
                    },
                    offsetY: -20,
                    style: {
                        fontSize: '12px',
                        colors: [isDarkMode ? '#fff' : '#333']
                    }
                },
                colors: ['#22c55e'],
                xaxis: {
                    categories: labels,
                    position: 'bottom',
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    },
                    crosshairs: {
                        fill: {
                            type: 'gradient',
                            gradient: {
                                colorFrom: '#22c55e',
                                colorTo: '#CED4DC',
                                stops: [0, 100],
                                opacityFrom: 0.4,
                                opacityTo: 0.5,
                            }
                        }
                    },
                    tooltip: {
                        enabled: true,
                    }
                },
                yaxis: {
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false,
                    },
                    labels: {
                        show: true,
                        formatter: function(val) {
                            return formatRupiah(val);
                        }
                    }
                },
                grid: {
                    borderColor: isDarkMode ? '#374151' : '#f3f4f6',
                    strokeDashArray: 3,
                    position: 'back',
                    xaxis: {
                        lines: {
                            show: false
                        }
                    },
                    yaxis: {
                        lines: {
                            show: true
                        }
                    }
                },
                tooltip: {
                    theme: isDarkMode ? 'dark' : 'light',
                    y: {
                        formatter: function(val) {
                            return formatRupiah(val);
                        }
                    }
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'light',
                        type: "vertical",
                        shadeIntensity: 0.3,
                        gradientToColors: ['#86efac'],
                        inverseColors: false,
                        opacityFrom: 0.9,
                        opacityTo: 0.7,
                        stops: [0, 90, 100]
                    }
                }
            };

            if (chartProfit) {
                chartProfit.updateOptions(options);
            } else {
                chartProfit = new ApexCharts(document.querySelector("#chartProfit"), options);
                chartProfit.render();
            }
        }

        // Create Product Chart
        function createProdukChart(labels, data) {
            const options = {
                series: [{
                    name: 'Jumlah Terjual',
                    data: data
                }],
                chart: {
                    type: 'bar',
                    height: 320,
                    toolbar: {
                        show: true
                    },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800
                    }
                },
                theme: chartTheme,
                plotOptions: {
                    bar: {
                        borderRadius: 8,
                        horizontal: true,
                        distributed: true,
                        dataLabels: {
                            position: 'center'
                        }
                    }
                },
                colors: ['#8b5cf6', '#a78bfa', '#c4b5fd', '#ddd6fe', '#ede9fe'],
                dataLabels: {
                    enabled: true,
                    textAnchor: 'start',
                    style: {
                        colors: ['#fff']
                    },
                    formatter: function(val, opt) {
                        return opt.w.globals.labels[opt.dataPointIndex] + ": " + val;
                    },
                    offsetX: 0,
                    dropShadow: {
                        enabled: false
                    }
                },
                xaxis: {
                    categories: labels
                },
                yaxis: {
                    labels: {
                        show: false
                    }
                },
                grid: {
                    borderColor: isDarkMode ? '#374151' : '#f3f4f6',
                    strokeDashArray: 3
                },
                tooltip: {
                    theme: isDarkMode ? 'dark' : 'light',
                    y: {
                        formatter: function(val) {
                            return val + " unit";
                        },
                        title: {
                            formatter: function() {
                                return '';
                            }
                        }
                    }
                },
                legend: {
                    show: false
                }
            };

            if (chartProduk) {
                chartProduk.updateOptions(options);
            } else {
                chartProduk = new ApexCharts(document.querySelector("#chartProduk"), options);
                chartProduk.render();
            }
        }

        // Create Trend Chart
        function createTrendChart(labels, data) {
            const options = {
                series: [{
                    name: 'Trend Penjualan',
                    data: data
                }],
                chart: {
                    type: 'area',
                    height: 320,
                    sparkline: {
                        enabled: false
                    },
                    toolbar: {
                        show: true
                    }
                },
                theme: chartTheme,
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        inverseColors: false,
                        opacityFrom: 0.5,
                        opacityTo: 0,
                        stops: [0, 90, 100]
                    }
                },
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    categories: labels,
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    }
                },
                yaxis: {
                    labels: {
                        formatter: function(val) {
                            return formatRupiah(val);
                        }
                    }
                },
                grid: {
                    borderColor: isDarkMode ? '#374151' : '#f3f4f6',
                    strokeDashArray: 3
                },
                tooltip: {
                    theme: isDarkMode ? 'dark' : 'light',
                    y: {
                        formatter: function(val) {
                            return formatRupiah(val);
                        }
                    }
                },
                colors: ['#3b82f6']
            };

            if (chartTrend) {
                chartTrend.updateOptions(options);
            } else {
                chartTrend = new ApexCharts(document.querySelector("#chartTrend"), options);
                chartTrend.render();
            }
        }

        // Create Comparison Chart (Donut)
        function createComparisonChart() {
            const options = {
                series: [44, 55, 41, 17, 15],
                chart: {
                    type: 'donut',
                    height: 320
                },
                theme: chartTheme,
                labels: ['Elektronik', 'Fashion', 'Makanan', 'Kesehatan', 'Lainnya'],
                colors: ['#3b82f6', '#22c55e', '#f59e0b', '#ef4444', '#8b5cf6'],
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%',
                            labels: {
                                show: true,
                                name: {
                                    fontSize: '16px',
                                    fontWeight: 600
                                },
                                value: {
                                    fontSize: '14px',
                                    fontWeight: 400,
                                    formatter: function(val) {
                                        return val + '%';
                                    }
                                },
                                total: {
                                    show: true,
                                    showAlways: false,
                                    label: 'Total',
                                    fontSize: '22px',
                                    fontWeight: 600,
                                    color: isDarkMode ? '#fff' : '#373d3f'
                                }
                            }
                        }
                    }
                },
                dataLabels: {
                    enabled: false
                },
                legend: {
                    position: 'bottom',
                    horizontalAlign: 'center'
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 300
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            if (chartComparison) {
                chartComparison.updateOptions(options);
            } else {
                chartComparison = new ApexCharts(document.querySelector("#chartComparison"), options);
                chartComparison.render();
            }
        }

        // Get URL parameters for API calls
        function getUrlParams() {
            const range = document.getElementById('filter-range').value;
            const tanggal = document.getElementById('tanggal-spesifik').value;
            const startDate = document.getElementById('tanggal-awal').value;
            const endDate = document.getElementById('tanggal-akhir').value;

            let params = `range=${range}`;
            if (range === 'harian' && tanggal) {
                params += `&tanggal=${tanggal}`;
            } else if (range === 'kustom' && startDate && endDate) {
                params += `&start_date=${startDate}&end_date=${endDate}`;
            }
            return params;
        }

        // Update summary statistics with animation
        async function updateSummary() {
            try {
                const params = getUrlParams();
                const response = await fetch(`/grafik-penjualan/summary?${params}`);

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const summary = await response.json();

                // Animate number updates
                animateValue('total-profit', 0, summary.total_profit, 1000, true);
                animateValue('total-penjualan', 0, summary.total_penjualan, 1000, true);
                animateValue('total-transaksi', 0, summary.total_transaksi, 1000, false);
                animateValue('total-produk', 0, summary.total_produk_terjual, 1000, false, ' unit');

                document.getElementById('periode-text').textContent = summary.periode;

                // Show change indicators (mock data for demo)
                updateChangeIndicator('profit-change', 12.5, true);
                updateChangeIndicator('penjualan-change', 8.3, true);
                updateChangeIndicator('transaksi-change', -2.1, false);
                updateChangeIndicator('produk-change', 15.7, true);

            } catch (error) {
                console.error('Error updating summary:', error);
            }
        }

        // Animate number counting
        function animateValue(id, start, end, duration, isCurrency, suffix = '') {
            const element = document.getElementById(id);
            const range = end - start;
            const startTime = performance.now();

            function updateValue(currentTime) {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                const currentValue = Math.floor(start + range * easeOutQuart(progress));

                if (isCurrency) {
                    element.textContent = formatRupiah(currentValue) + suffix;
                } else {
                    element.textContent = currentValue.toLocaleString('id-ID') + suffix;
                }

                if (progress < 1) {
                    requestAnimationFrame(updateValue);
                }
            }

            requestAnimationFrame(updateValue);
        }

        // Easing function for smooth animation
        function easeOutQuart(t) {
            return 1 - Math.pow(1 - t, 4);
        }

        // Update change indicators
        function updateChangeIndicator(id, percentage, isPositive) {
            const element = document.getElementById(id);
            const arrow = isPositive ? '↑' : '↓';
            const color = isPositive ? 'text-green-200' : 'text-red-200';
            element.innerHTML = `<span class="${color}">${arrow} ${Math.abs(percentage)}%</span> dari periode sebelumnya`;
        }

        // Update all charts
        async function updateCharts() {
            try {
                const params = getUrlParams();

                // Fetch profit data
                const profitResponse = await fetch(`/grafik-penjualan/profit?${params}`);
                if (!profitResponse.ok) {
                    throw new Error(`Profit API error: ${profitResponse.status}`);
                }
                const profitData = await profitResponse.json();

                // Fetch product data
                const produkResponse = await fetch(`/grafik-penjualan/produk-terlaris?${params}`);
                if (!produkResponse.ok) {
                    throw new Error(`Produk API error: ${produkResponse.status}`);
                }
                const produkData = await produkResponse.json();

                // Render charts
                const profitLabels = profitData.map(item => item.label);
                const profitValues = profitData.map(item => item.total_laba);
                createProfitChart(profitLabels, profitValues);

                const produkLabels = produkData.map(item => item.label);
                const produkValues = produkData.map(item => item.total_terjual);
                createProdukChart(produkLabels, produkValues);

                // Create trend chart with same data
                createTrendChart(profitLabels, profitValues);

                // Create comparison chart
                createComparisonChart();

            } catch (error) {
                console.error('Error updating charts:', error);
                alert('Terjadi kesalahan saat memuat data. Silakan coba lagi.');
            }
        }

        // Main update function
        async function fetchDataAndRender() {
            showLoading();
            try {
                // Check if ApexCharts is loaded
                if (typeof ApexCharts === 'undefined') {
                    throw new Error('ApexCharts belum dimuat. Periksa koneksi internet.');
                }

                await Promise.all([
                    updateSummary(),
                    updateCharts()
                ]);

            } catch (error) {
                console.error('Error fetching data:', error);
                alert('Terjadi kesalahan saat memuat data. Silakan coba lagi.');
            } finally {
                hideLoading();
            }
        }

        // Toggle date inputs based on range selection
        function toggleDateInputs() {
            const range = document.getElementById('filter-range').value;
            const tanggalSpesifik = document.getElementById('tanggal-spesifik-container');
            const tanggalAwal = document.getElementById('tanggal-awal-container');
            const tanggalAkhir = document.getElementById('tanggal-akhir-container');

            // Hide all first
            tanggalSpesifik.classList.add('hidden');
            tanggalAwal.classList.add('hidden');
            tanggalAkhir.classList.add('hidden');

            // Show relevant inputs
            if (range === 'harian') {
                tanggalSpesifik.classList.remove('hidden');
            } else if (range === 'kustom') {
                tanggalAwal.classList.remove('hidden');
                tanggalAkhir.classList.remove('hidden');
            }
        }

        // Export functionality
        function exportCharts() {
            // Get current data
            const range = document.getElementById('filter-range').value;
            const periode = document.getElementById('periode-text').textContent;

            // You can implement PDF export here using jsPDF or similar library
            alert('Export functionality akan segera tersedia!');
        }

        // Toggle chart type
        function toggleChartType() {
            currentChartType = currentChartType === 'bar' ? 'line' : 'bar';
            fetchDataAndRender();
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Check if ApexCharts is available
            if (typeof ApexCharts === 'undefined') {
                console.error('ApexCharts tidak tersedia! Periksa koneksi internet.');
                return;
            }

            // Initial load
            setTimeout(() => {
                fetchDataAndRender();
            }, 100);

            // Filter change event
            document.getElementById('filter-range').addEventListener('change', toggleDateInputs);

            // Button events
            document.getElementById('btn-lihat').addEventListener('click', fetchDataAndRender);
            document.getElementById('btn-refresh').addEventListener('click', fetchDataAndRender);
            document.getElementById('btn-export').addEventListener('click', exportCharts);
            document.getElementById('btn-chart-type').addEventListener('click', toggleChartType);

            // Date input events
            document.getElementById('tanggal-spesifik').addEventListener('change', fetchDataAndRender);
            document.getElementById('tanggal-awal').addEventListener('change', fetchDataAndRender);
            document.getElementById('tanggal-akhir').addEventListener('change', fetchDataAndRender);

            // Zoom button events
            document.querySelectorAll('.chart-zoom-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const chartType = this.dataset.chart;
                    if (chartType === 'profit' && chartProfit) {
                        chartProfit.resetSeries();
                    } else if (chartType === 'produk' && chartProduk) {
                        chartProduk.resetSeries();
                    }
                });
            });

            // Auto refresh every 5 minutes
            setInterval(() => {
                fetchDataAndRender();
            }, 300000);
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            if (chartProfit) chartProfit.updateOptions({
                chart: {
                    width: '100%'
                }
            });
            if (chartProduk) chartProduk.updateOptions({
                chart: {
                    width: '100%'
                }
            });
            if (chartTrend) chartTrend.updateOptions({
                chart: {
                    width: '100%'
                }
            });
            if (chartComparison) chartComparison.updateOptions({
                chart: {
                    width: '100%'
                }
            });
        });
    </script>
</x-app-layout>
