<div x-data="{
    renderCharts() {
        const formatRp = v => 'Rp ' + v.toLocaleString('id-ID');
        const opts = (labels, data, label, isRupiah = true) => ({
            type: 'bar',
            data: { labels, datasets: [{ label, data, borderRadius: 5 }] },
            options: {
                scales: { y: { beginAtZero: true, ticks: { callback: val => isRupiah ? formatRp(val) : val } } }
            }
        });
        window.chartHarian && chartHarian.destroy();
        window.chartMingguan && chartMingguan.destroy();
        window.chartBulanan && chartBulanan.destroy();
        window.chartProdHarian && chartProdHarian.destroy();
        window.chartProdMingguan && chartProdMingguan.destroy();
        window.chartProdBulanan && chartProdBulanan.destroy();

        chartHarian = new Chart($refs.harian, opts(@js($labelHarian), @js($profitHarian), 'Profit Harian'));
        chartMingguan = new Chart($refs.mingguan, opts(@js($labelMingguan), @js($profitMingguan), 'Profit Mingguan'));
        chartBulanan = new Chart($refs.bulanan, opts(@js($labelBulanan), @js($profitBulanan), 'Profit Bulanan'));

        chartProdHarian = new Chart($refs.prodHarian, opts(@js($produkLabelHarian), @js($produkHarian), 'Terlaris Harian', false));
        chartProdMingguan = new Chart($refs.prodMingguan, opts(@js($produkLabelMingguan), @js($produkMingguan), 'Terlaris Mingguan', false));
        chartProdBulanan = new Chart($refs.prodBulanan, opts(@js($produkLabelBulanan), @js($produkBulanan), 'Terlaris Bulanan', false));
    }
}" x-init="renderCharts()">
    <div class="grid grid-cols-2 gap-6">
        <div>
            <label>Profit Harian</label>
            <canvas x-ref="harian"></canvas>
        </div>
        <div>
            <label>Profit Mingguan</label>
            <canvas x-ref="mingguan"></canvas>
        </div>
        <div>
            <label>Profit Bulanan</label>
            <canvas x-ref="bulanan"></canvas>
        </div>
        <div>
            <label>Produk Harian</label>
            <canvas x-ref="prodHarian"></canvas>
        </div>
        <div>
            <label>Produk Mingguan</label>
            <canvas x-ref="prodMingguan"></canvas>
        </div>
        <div>
            <label>Produk Bulanan</label>
            <canvas x-ref="prodBulanan"></canvas>
        </div>
    </div>
</div>


<script>
    function formatRp(value) {
        return 'Rp ' + value.toLocaleString('id-ID');
    }

    function chartOptions(labels, data, labelText, isRupiah = true) {
        return {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: labelText,
                    data,
                    borderRadius: 5,
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return isRupiah ? formatRp(value) : value;
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const v = context.parsed.y;
                                return isRupiah ? formatRp(v) : v;
                            }
                        }
                    }
                }
            }
        };
    }

    document.addEventListener('alpine:init', () => {
        Alpine.data('grafik', () => ({
            init() {
                // destruk chart lama
                window.chartHarian && chartHarian.destroy();
                window.chartMingguan && chartMingguan.destroy();
                window.chartBulanan && chartBulanan.destroy();

                // buat chart baru dengan Rp format
                chartHarian = new Chart(this.$refs.harian, chartOptions(
                    @js($labelHarian),
                    @js($profitHarian),
                    'Profit Harian',
                    true // true → format Rupiah
                ));

                chartMingguan = new Chart(this.$refs.mingguan, chartOptions(
                    @js($labelMingguan),
                    @js($profitMingguan),
                    'Profit Mingguan',
                    true
                ));

                chartBulanan = new Chart(this.$refs.bulanan, chartOptions(
                    @js($labelBulanan),
                    @js($profitBulanan),
                    'Profit Bulanan',
                    true
                ));
            }
        }));
    });
</script>
