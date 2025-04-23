<x-app-layout>
    <div class="p-6 space-y-6">

        {{-- Filter & Button --}}
        <div class="flex flex-wrap items-center gap-4">
            <label class="text-sm font-semibold text-gray-700">Rentang Waktu:</label>
            <select id="filter-range"
                class="border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                <option value="harian">Harian</option>
                <option value="bulanan">Bulanan</option>
                <option value="tahunan">Tahunan</option>
                <option value="kustom">Rentang Tanggal</option>
            </select>

            {{-- Tanggal untuk harian --}}
            <input type="date" id="tanggal-spesifik"
                class="border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 hidden" />

            {{-- Tanggal awal-akhir untuk kustom --}}
            <input type="date" id="tanggal-awal"
                class="border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 hidden" />
            <span class="text-gray-600 hidden" id="label-sampai">s/d</span>
            <input type="date" id="tanggal-akhir"
                class="border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 hidden" />

            <button id="btn-lihat" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
                Lihat Grafik
            </button>
        </div>

        {{-- Charts --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white border p-4 rounded-lg shadow">
                <h2 class="text-lg font-semibold text-red-800 mb-2">Profit Penjualan</h2>
                <canvas id="chartProfit" height="250"></canvas>
            </div>
            <div class="bg-white border p-4 rounded-lg shadow">
                <h2 class="text-lg font-semibold text-red-800 mb-2">Produk Terlaris</h2>
                <canvas id="chartProduk" height="250"></canvas>
            </div>
        </div>
    </div>

    {{-- Script langsung di sini --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function renderChart(canvasId, label, labels, data, color = 'rgba(255,99,132,0.6)', isRupiah = false) {
            const ctx = document.getElementById(canvasId).getContext('2d');

            if (window[canvasId] instanceof Chart) {
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
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: value => isRupiah ?
                                    'Rp ' + new Intl.NumberFormat('id-ID').format(value) : value
                            }
                        }
                    }
                }
            });
        }


        async function fetchDataAndRender() {
            const range = document.getElementById('filter-range').value;
            const tanggal = document.getElementById('tanggal-spesifik').value;
            const start = document.getElementById('tanggal-awal').value;
            const end = document.getElementById('tanggal-akhir').value;

            let urlParams = `range=${range}`;
            if (range === 'harian' && tanggal) {
                urlParams += `&tanggal=${tanggal}`;
            } else if (range === 'kustom' && start && end) {
                urlParams += `&start_date=${start}&end_date=${end}`;
            }

            const profitRes = await fetch(`/grafik-penjualan/profit?${urlParams}`).then(r => r.json());
            const produkRes = await fetch(`/grafik-penjualan/produk-terlaris?${urlParams}`).then(r => r.json());

            const profitLabels = profitRes.map(item => item.label);
            const profitData = profitRes.map(item => item.total_laba);

            const produkLabels = produkRes.map(item => item.label);
            const produkData = produkRes.map(item => item.total_terjual);

            renderChart('chartProfit', 'Laba Bersih', profitLabels, profitData, 'rgba(75,192,192,0.6)');
            renderChart('chartProduk', 'Jumlah Terjual', produkLabels, produkData, 'rgba(153,102,255,0.6)');
        }

        document.getElementById('btn-lihat').addEventListener('click', fetchDataAndRender);
        window.addEventListener('load', fetchDataAndRender);

        document.getElementById('filter-range').addEventListener('change', function() {
            const spesifik = document.getElementById('tanggal-spesifik');
            const awal = document.getElementById('tanggal-awal');
            const akhir = document.getElementById('tanggal-akhir');
            const labelSampai = document.getElementById('label-sampai');

            spesifik.classList.add('hidden');
            awal.classList.add('hidden');
            akhir.classList.add('hidden');
            labelSampai.classList.add('hidden');

            if (this.value === 'harian') {
                spesifik.classList.remove('hidden');
            } else if (this.value === 'kustom') {
                awal.classList.remove('hidden');
                akhir.classList.remove('hidden');
                labelSampai.classList.remove('hidden');
            }
        });
    </script>
</x-app-layout>
