<div x-data="{
    renderCharts() {
        // Format Rupiah
        const formatRp = value => 'Rp ' + value.toLocaleString('id-ID');

        // Opsi umum untuk Chart.js
        const chartOpts = (labels, data, labelText, isRupiah = true) => ({
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: labelText,
                    data,
                    borderRadius: 5,
                    backgroundColor: '#3B82F6'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: val => isRupiah ? formatRp(val) : val
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: ctx => {
                                const v = ctx.parsed.y;
                                return isRupiah ? formatRp(v) : v;
                            }
                        }
                    }
                }
            }
        });

        // Hancurkan chart yang sudah ada (jika ada)
        if (window.chartHarian) window.chartHarian.destroy();
        if (window.chartMingguan) window.chartMingguan.destroy();
        if (window.chartBulanan) window.chartBulanan.destroy();
        if (window.chartProdHarian) window.chartProdHarian.destroy();
        if (window.chartProdMingguan) window.chartProdMingguan.destroy();
        if (window.chartProdBulanan) window.chartProdBulanan.destroy();

        // 1) Chart Profit Harian
        chartHarian = new Chart(this.$refs.harian, chartOpts(
            @js($labelHarian),
            @js($profitHarian),
            'Profit Harian',
            true
        ));

        // 2) Chart Profit Mingguan
        chartMingguan = new Chart(this.$refs.mingguan, chartOpts(
            @js($labelMingguan),
            @js($profitMingguan),
            'Profit Mingguan',
            true
        ));

        // 3) Chart Profit Bulanan
        chartBulanan = new Chart(this.$refs.bulanan, chartOpts(
            @js($labelBulanan),
            @js($profitBulanan),
            'Profit Bulanan',
            true
        ));

        // 4) Chart Produk Terlaris Harian (hanya angka, bukan Rupiah)
        chartProdHarian = new Chart(this.$refs.prodHarian, chartOpts(
            @js($produkLabelHarian),
            @js($produkHarian),
            'Top 5 Produk Harian',
            false
        ));

        // 5) Chart Produk Terlaris Mingguan
        chartProdMingguan = new Chart(this.$refs.prodMingguan, chartOpts(
            @js($produkLabelMingguan),
            @js($produkMingguan),
            'Top 5 Produk Mingguan',
            false
        ));

        // 6) Chart Produk Terlaris Bulanan
        chartProdBulanan = new Chart(this.$refs.prodBulanan, chartOpts(
            @js($produkLabelBulanan),
            @js($produkBulanan),
            'Top 5 Produk Bulanan',
            false
        ));
    }
}" x-init="renderCharts()">

    <div class="space-y-8">
        {{-- Bagian Profit --}}
        <div class="grid grid-cols-3 gap-6">
            <div class="bg-white p-4 rounded shadow">
                <h3 class="font-semibold mb-2">Profit Harian (7 hari)</h3>
                <div class="h-48">
                    <canvas x-ref="harian"></canvas>
                </div>
            </div>
            <div class="bg-white p-4 rounded shadow">
                <h3 class="font-semibold mb-2">Profit Mingguan (bulan ini)</h3>
                <div class="h-48">
                    <canvas x-ref="mingguan"></canvas>
                </div>
            </div>
            <div class="bg-white p-4 rounded shadow">
                <h3 class="font-semibold mb-2">Profit Bulanan (tahun ini)</h3>
                <div class="h-48">
                    <canvas x-ref="bulanan"></canvas>
                </div>
            </div>
        </div>

        {{-- Bagian Produk Terlaris --}}
        <div class="grid grid-cols-3 gap-6">
            <div class="bg-white p-4 rounded shadow">
                <h3 class="font-semibold mb-2">Top 5 Produk Harian</h3>
                <div class="h-48">
                    <canvas x-ref="prodHarian"></canvas>
                </div>
            </div>
            <div class="bg-white p-4 rounded shadow">
                <h3 class="font-semibold mb-2">Top 5 Produk Mingguan</h3>
                <div class="h-48">
                    <canvas x-ref="prodMingguan"></canvas>
                </div>
            </div>
            <div class="bg-white p-4 rounded shadow">
                <h3 class="font-semibold mb-2">Top 5 Produk Bulanan</h3>
                <div class="h-48">
                    <canvas x-ref="prodBulanan"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
