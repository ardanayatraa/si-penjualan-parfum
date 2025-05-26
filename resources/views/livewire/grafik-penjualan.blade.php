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
