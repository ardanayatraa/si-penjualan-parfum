<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Grafik Penjualan</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 15mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            background: white;
        }

        .container {
            width: 100%;
            padding: 15px;
        }

        /* Header */
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header .period {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }

        .header .print-info {
            font-size: 10px;
            color: #888;
        }

        /* Summary Section */
        .summary-section {
            margin-bottom: 25px;
            padding: 15px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }

        .summary-section h2 {
            font-size: 14px;
            margin-bottom: 15px;
            text-align: center;
            text-transform: uppercase;
            color: #333;
        }

        .summary-stats {
            display: table;
            width: 100%;
            border-spacing: 10px;
        }

        .stat-item {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 12px;
            border: 1px solid #ddd;
            background-color: white;
        }

        .stat-label {
            font-size: 10px;
            color: #666;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .stat-value {
            font-size: 13px;
            font-weight: bold;
            color: #333;
        }

        /* Data Tables */
        .data-section {
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            padding: 8px;
            background-color: #e8f4f8;
            border: 1px solid #333;
            text-align: center;
            text-transform: uppercase;
        }

        .data-container {
            display: table;
            width: 100%;
            border-spacing: 15px;
        }

        .data-table {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background-color: #f5f5f5;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
        }

        td {
            font-size: 11px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .currency {
            font-family: 'DejaVu Sans Mono', monospace;
        }

        /* Colors */
        .profit-color {
            color: #2d7a2d;
        }

        .product-color {
            color: #7c3aed;
        }

        /* Chart Placeholder */
        .chart-placeholder {
            width: 100%;
            height: 200px;
            border: 2px dashed #ccc;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            font-style: italic;
            margin-bottom: 15px;
            background-color: #f9f9f9;
        }

        /* Footer Notes */
        .footer-notes {
            margin-top: 25px;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 15px;
        }

        .footer-notes ul {
            margin-left: 15px;
            margin-top: 8px;
        }

        .footer-notes li {
            margin-bottom: 3px;
            line-height: 1.4;
        }

        /* No data message */
        .no-data {
            text-align: center;
            padding: 20px;
            font-style: italic;
            color: #666;
            background-color: #f8f9fa;
            border: 1px dashed #ddd;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Laporan Grafik Penjualan</h1>
            <div class="period">
                @if ($range === 'harian' && $tanggal)
                    Tanggal: {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}
                @elseif($range === 'kustom' && $startDate && $endDate)
                    Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} -
                    {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
                @else
                    Periode: {{ ucfirst($range) }}
                @endif
            </div>
            <div class="print-info">
                Dicetak pada: {{ now()->format('d/m/Y H:i:s') }} |
                Tipe Laporan: {{ ucfirst($range) }}
            </div>
        </div>

        <!-- Summary Statistics -->
        <div class="summary-section">
            <h2>Ringkasan Penjualan</h2>
            <div class="summary-stats">
                <div class="stat-item">
                    <div class="stat-label">Total Profit</div>
                    <div class="stat-value profit-color">Rp
                        {{ number_format($summary->total_profit ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Total Penjualan</div>
                    <div class="stat-value">Rp {{ number_format($summary->total_penjualan ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Total Transaksi</div>
                    <div class="stat-value">{{ number_format($summary->total_transaksi ?? 0) }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Produk Terjual</div>
                    <div class="stat-value product-color">{{ number_format($summary->total_produk_terjual ?? 0) }} unit
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart Placeholders -->
        <div class="data-section">
            <div class="section-title">Visualisasi Data</div>
            <div class="data-container">
                <div class="data-table">
                    <div class="chart-placeholder">
                        <div>
                            <strong>Grafik Profit Penjualan</strong><br>
                            <small>Lihat versi digital untuk grafik interaktif</small>
                        </div>
                    </div>
                </div>
                <div class="data-table">
                    <div class="chart-placeholder">
                        <div>
                            <strong>Grafik Produk Terlaris</strong><br>
                            <small>Lihat versi digital untuk grafik interaktif</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Tables -->
        <div class="data-section">
            <div class="section-title">Detail Data</div>
            <div class="data-container">
                <!-- Profit Data Table -->
                <div class="data-table">
                    <h3 style="font-size: 12px; margin-bottom: 10px; color: #2d7a2d;">Data Profit</h3>
                    @if (count($profitData) > 0)
                        <table>
                            <thead>
                                <tr>
                                    <th style="width: 60%;">Periode</th>
                                    <th style="width: 40%;" class="text-right">Profit (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($profitData as $item)
                                    <tr>
                                        <td>{{ $item->label }}</td>
                                        <td class="text-right currency profit-color">
                                            {{ number_format($item->total_laba, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr style="background-color: #f0f8f0; font-weight: bold;">
                                    <td>Total</td>
                                    <td class="text-right currency profit-color">
                                        {{ number_format(collect($profitData)->sum('total_laba'), 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    @else
                        <div class="no-data">
                            Tidak ada data profit untuk periode ini
                        </div>
                    @endif
                </div>

                <!-- Product Data Table -->
                <div class="data-table">
                    <h3 style="font-size: 12px; margin-bottom: 10px; color: #7c3aed;">Data Produk Terlaris</h3>
                    @if (count($produkData) > 0)
                        <table>
                            <thead>
                                <tr>
                                    <th style="width: 10%;" class="text-center">No</th>
                                    <th style="width: 60%;">Nama Produk</th>
                                    <th style="width: 30%;" class="text-right">Terjual</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($produkData as $index => $item)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>{{ $item->label }}</td>
                                        <td class="text-right product-color">
                                            {{ number_format($item->total_terjual) }} unit
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr style="background-color: #f0f0f8; font-weight: bold;">
                                    <td colspan="2">Total</td>
                                    <td class="text-right product-color">
                                        {{ number_format(collect($produkData)->sum('total_terjual')) }} unit
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    @else
                        <div class="no-data">
                            Tidak ada data produk untuk periode ini
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Analysis Summary -->
        @if (count($profitData) > 0 || count($produkData) > 0)
            <div class="summary-section">
                <h2>Analisis Singkat</h2>
                <div style="font-size: 11px; line-height: 1.6;">
                    @if (count($profitData) > 0)
                        @php
                            $maxProfit = collect($profitData)->max('total_laba');
                            $bestPeriod = collect($profitData)->firstWhere('total_laba', $maxProfit);
                            $totalProfit = collect($profitData)->sum('total_laba');
                            $avgProfit = $totalProfit / count($profitData);
                        @endphp
                        <p><strong>Profit Tertinggi:</strong> {{ $bestPeriod->label ?? '-' }} dengan profit Rp
                            {{ number_format($maxProfit, 0, ',', '.') }}</p>
                        <p><strong>Rata-rata Profit:</strong> Rp {{ number_format($avgProfit, 0, ',', '.') }} per
                            periode</p>
                    @endif

                    @if (count($produkData) > 0)
                        @php
                            $topProduct = collect($produkData)->first();
                            $totalSold = collect($produkData)->sum('total_terjual');
                        @endphp
                        <p><strong>Produk Terlaris:</strong> {{ $topProduct->label ?? '-' }}
                            ({{ number_format($topProduct->total_terjual ?? 0) }} unit)</p>
                        <p><strong>Total Produk Terjual:</strong> {{ number_format($totalSold) }} unit</p>
                    @endif
                </div>
            </div>
        @endif

        <!-- Footer Notes -->
        <div class="footer-notes">
            <p><strong>Catatan:</strong></p>
            <ul>
                <li>Laporan ini dibuat berdasarkan data transaksi yang telah selesai</li>
                <li>Profit dihitung dari laba bersih setelah dikurangi harga pokok penjualan</li>
                <li>Data produk terlaris menampilkan 5 produk dengan penjualan tertinggi</li>
                <li>Untuk melihat grafik interaktif, silakan akses versi digital di aplikasi</li>
                <li>Periode laporan: {{ $summary->periode ?? ucfirst($range) }}</li>
                <li>Data dicetak pada: {{ now()->format('d F Y, H:i:s') }}</li>
            </ul>
        </div>
    </div>
</body>

</html>
