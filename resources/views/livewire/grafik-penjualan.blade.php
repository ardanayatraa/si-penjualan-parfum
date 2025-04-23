<div x-data="{
    renderAllCharts() {
        const formatRupiah = value => 'Rp ' + value.toLocaleString('id-ID');

        const chartOptions = (label, data, color, labelText, isRupiah = true) => ({
            type: 'bar',
            data: {
                labels: label,
                datasets: [{
                    label: labelText,
                    data: data,
                    backgroundColor: color,
                    borderRadius: 5,
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: value => isRupiah ? formatRupiah(value) : value
                        }
                    }
                }
            }
        });

        // Hancurkan chart lama jika ada
        if (window.chartHarian) chartHarian.destroy();
        if (window.chartMingguan) chartMingguan.destroy();
        if (window.chartBulanan) chartBulanan.destroy();

        if (window.chartProdukHarian) chartProdukHarian.destroy();
        if (window.chartProdukMingguan) chartProdukMingguan.destroy();
        if (window.chartProdukBulanan) chartProdukBulanan.destroy();

        // Render chart profit
        chartHarian = new Chart(this.$refs.chartHarian, chartOptions(@js($labelHarian), @js($profitHarian), 'rgba(75, 192, 192, 0.6)', 'Profit Harian'));
        chartMingguan = new Chart(this.$refs.chartMingguan, chartOptions(@js($labelMingguan), @js($profitMingguan), 'rgba(153, 102, 255, 0.6)', 'Profit Mingguan'));
        chartBulanan = new Chart(this.$refs.chartBulanan, chartOptions(@js($labelBulanan), @js($profitBulanan), 'rgba(255, 205, 86, 0.6)', 'Profit Bulanan'));

        // Render chart produk terlaris (tanpa format Rp)
        chartProdukHarian = new Chart(this.$refs.chartProdukHarian, chartOptions(@js($produkLabelHarian), @js($produkHarian), 'rgba(255, 99, 132, 0.6)', 'Produk Terlaris Harian', false));
        chartProdukMingguan = new Chart(this.$refs.chartProdukMingguan, chartOptions(@js($produkLabelMingguan), @js($produkMingguan), 'rgba(54, 162, 235, 0.6)', 'Produk Terlaris Mingguan', false));
        chartProdukBulanan = new Chart(this.$refs.chartProdukBulanan, chartOptions(@js($produkLabelBulanan), @js($produkBulanan), 'rgba(255, 159, 64, 0.6)', 'Produk Terlaris Bulanan', false));
    }
}" x-init="renderAllCharts()">
    <div class="mb-6 max-w-sm">
        <label class="block font-medium mb-1">Pilih Rentang Waktu</label>
        <select id="chartRangeSelect" class="w-full border px-4 py-2 rounded" wire:change="updateDataForBulanLalu">
            <option value="harian">7 Hari Terakhir</option>
            <option value="mingguan">Bulan Ini</option>
            <option value="bulanan">Tahun Ini</option>
            <option value="bulanLalu">Bulan Lalu</option>
        </select>
    </div>
</div>
