{{-- resources/views/exports/stok-pdf.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Stok Barang</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 20px;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }

        .header h1 {
            margin: 0 0 8px;
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header .export-info {
            margin: 5px 0;
            font-size: 12px;
            color: #666;
        }

        .statistics-section {
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            gap: 15px;
        }

        .stats-card {
            flex: 1;
            background-color: #f8f9fa;
            padding: 12px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .stats-card h3 {
            margin: 0 0 8px;
            font-size: 11px;
            color: #333;
            text-transform: uppercase;
        }

        .stats-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
            font-size: 9px;
        }

        .stats-item.highlight {
            font-weight: bold;
            font-size: 10px;
            color: #2c5aa0;
        }

        .status-overview {
            margin-bottom: 20px;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .status-overview h3 {
            margin: 0 0 10px;
            font-size: 12px;
            color: #333;
            text-transform: uppercase;
        }

        .status-grid {
            display: flex;
            justify-content: space-around;
            text-align: center;
        }

        .status-item {
            flex: 1;
        }

        .status-number {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .status-label {
            font-size: 9px;
            color: #666;
        }

        .status-aman .status-number {
            color: #28a745;
        }

        .status-menipis .status-number {
            color: #ffc107;
        }

        .status-habis .status-number {
            color: #dc3545;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 4px;
            text-align: left;
        }

        th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: center;
            font-size: 8px;
        }

        td {
            font-size: 8px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .total-row {
            background-color: #e9ecef;
            font-weight: bold;
        }

        .stock-status {
            padding: 1px 4px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: bold;
        }

        .stock-aman {
            background-color: #d4edda;
            color: #155724;
        }

        .stock-menipis {
            background-color: #fff3cd;
            color: #856404;
        }

        .stock-habis {
            background-color: #f8d7da;
            color: #721c24;
        }

        .analysis-section {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }

        .analysis-card {
            flex: 1;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .analysis-card h3 {
            margin: 0 0 10px;
            font-size: 11px;
            color: #333;
            text-transform: uppercase;
        }

        .analysis-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
            font-size: 9px;
        }

        .footer {
            position: fixed;
            bottom: 20px;
            width: 100%;
            text-align: center;
            font-size: 8px;
            color: #666;
        }

        .no-data {
            text-align: center;
            font-style: italic;
            color: #666;
            padding: 40px;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Laporan Stok Barang</h1>
        <div class="export-info">
            Jenis Export: {{ ucfirst($exportType) }} |
            Dicetak: {{ now()->format('d M Y H:i') }} |
            Total Items: {{ $statistics['totalItems'] }}
        </div>
    </div>

    @if ($data->isEmpty())
        <div class="no-data">
            <p>Tidak ada data stok untuk kriteria yang dipilih.</p>
        </div>
    @else
        {{-- Statistics Section --}}
        <div class="statistics-section">
            <div class="stats-card">
                <h3>Ringkasan Stok</h3>
                <div class="stats-item">
                    <span>Total Item:</span>
                    <span>{{ number_format($statistics['totalItems'], 0, ',', '.') }}</span>
                </div>
                <div class="stats-item">
                    <span>Total Stok:</span>
                    <span>{{ number_format($statistics['totalStok'], 0, ',', '.') }}</span>
                </div>
                <div class="stats-item highlight">
                    <span>Nilai Stok:</span>
                    <span>Rp {{ number_format($statistics['totalNilaiStok'], 0, ',', '.') }}</span>
                </div>
                <div class="stats-item">
                    <span>Rata-rata/Item:</span>
                    <span>Rp {{ number_format($statistics['rataRataNilaiPerItem'], 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="stats-card">
                <h3>Transaksi Pembelian</h3>
                <div class="stats-item">
                    <span>Total Qty Beli:</span>
                    <span>{{ number_format($statistics['totalQtyBeli'], 0, ',', '.') }}</span>
                </div>
                <div class="stats-item highlight">
                    <span>Nilai Pembelian:</span>
                    <span>Rp {{ number_format($statistics['totalNilaiBeli'], 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="stats-card">
                <h3>Transaksi Penjualan</h3>
                <div class="stats-item">
                    <span>Total Qty Jual:</span>
                    <span>{{ number_format($statistics['totalQtyJual'], 0, ',', '.') }}</span>
                </div>
                <div class="stats-item highlight">
                    <span>Nilai Penjualan:</span>
                    <span>Rp {{ number_format($statistics['totalNilaiJual'], 0, ',', '.') }}</span>
                </div>
                <div class="stats-item">
                    <span>Turnover:</span>
                    <span>{{ number_format($statistics['overallTurnover'], 1) }}%</span>
                </div>
            </div>
        </div>

        {{-- Stock Status Overview --}}
        <div class="status-overview">
            <h3>Status Stok</h3>
            <div class="status-grid">
                <div class="status-item status-aman">
                    <div class="status-number">{{ $stockStatus['aman'] }}</div>
                    <div class="status-label">Stok Aman<br>(> 10 unit)</div>
                </div>
                <div class="status-item status-menipis">
                    <div class="status-number">{{ $stockStatus['menipis'] }}</div>
                    <div class="status-label">Stok Menipis<br>(1-10 unit)</div>
                </div>
                <div class="status-item status-habis">
                    <div class="status-number">{{ $stockStatus['habis'] }}</div>
                    <div class="status-label">Stok Habis<br>(0 unit)</div>
                </div>
            </div>
        </div>

        {{-- Main Data Table --}}
        <table>
            <thead>
                <tr>
                    <th style="width: 3%;">No</th>
                    <th style="width: 15%;">Nama Barang</th>
                    <th style="width: 10%;">Supplier</th>
                    <th style="width: 8%;">Harga Beli</th>
                    <th style="width: 8%;">Harga Jual</th>
                    <th style="width: 6%;">Stok</th>
                    <th style="width: 8%;">Nilai Stok</th>
                    <th style="width: 6%;">Qty Beli</th>
                    <th style="width: 8%;">Nilai Beli</th>
                    <th style="width: 6%;">Qty Jual</th>
                    <th style="width: 8%;">Nilai Jual</th>
                    <th style="width: 6%;">Turnover</th>
                    <th style="width: 6%;">Margin</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $index => $item)
                    @php
                        $nilaiStok = $item->stok * $item->harga_beli;
                        $statusStok = match (true) {
                            $item->stok == 0 => 'habis',
                            $item->stok <= 10 => 'menipis',
                            default => 'aman',
                        };
                        $turnover =
                            ($item->qty_pembelian ?? 0) > 0
                                ? (($item->qty_penjualan ?? 0) / $item->qty_pembelian) * 100
                                : 0;
                        $margin =
                            $item->harga_beli > 0
                                ? (($item->harga_jual - $item->harga_beli) / $item->harga_beli) * 100
                                : 0;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $item->nama_barang }}</td>
                        <td>{{ $item->supplier->nama_supplier ?? '-' }}</td>
                        <td class="text-right">Rp {{ number_format($item->harga_beli, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <span class="stock-status stock-{{ $statusStok }}">{{ $item->stok }}</span>
                        </td>
                        <td class="text-right">Rp {{ number_format($nilaiStok, 0, ',', '.') }}</td>
                        <td class="text-center">{{ number_format($item->qty_pembelian ?? 0, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($item->nilai_pembelian ?? 0, 0, ',', '.') }}</td>
                        <td class="text-center">{{ number_format($item->qty_penjualan ?? 0, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($item->nilai_penjualan ?? 0, 0, ',', '.') }}</td>
                        <td class="text-center">{{ number_format($turnover, 1) }}%</td>
                        <td class="text-center">{{ number_format($margin, 1) }}%</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="5" class="text-center"><strong>TOTAL</strong></td>
                    <td class="text-center"><strong>{{ number_format($statistics['totalStok'], 0, ',', '.') }}</strong>
                    </td>
                    <td class="text-right"><strong>Rp
                            {{ number_format($statistics['totalNilaiStok'], 0, ',', '.') }}</strong></td>
                    <td class="text-center">
                        <strong>{{ number_format($statistics['totalQtyBeli'], 0, ',', '.') }}</strong>
                    </td>
                    <td class="text-right"><strong>Rp
                            {{ number_format($statistics['totalNilaiBeli'], 0, ',', '.') }}</strong></td>
                    <td class="text-center">
                        <strong>{{ number_format($statistics['totalQtyJual'], 0, ',', '.') }}</strong>
                    </td>
                    <td class="text-right"><strong>Rp
                            {{ number_format($statistics['totalNilaiJual'], 0, ',', '.') }}</strong></td>
                    <td class="text-center"><strong>{{ number_format($statistics['overallTurnover'], 1) }}%</strong>
                    </td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        {{-- Analysis Section --}}
        <div class="analysis-section">
            {{-- Supplier Analysis --}}
            <div class="analysis-card">
                <h3>Analisis per Supplier</h3>
                @foreach ($supplierStats->take(5) as $supplier => $stats)
                    <div class="analysis-item">
                        <span>{{ $supplier ?: 'Tidak ada supplier' }}:</span>
                        <span>{{ $stats['count'] }} item</span>
                    </div>
                @endforeach
            </div>

            {{-- Top Products by Value --}}
            <div class="analysis-card">
                <h3>Produk Nilai Stok Tertinggi</h3>
                @foreach ($topProductsByValue->take(5) as $product)
                    @php
                        $nilaiStok = $product->stok * $product->harga_beli;
                    @endphp
                    <div class="analysis-item">
                        <span>{{ $product->nama_barang }}:</span>
                        <span>Rp {{ number_format($nilaiStok, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>

            {{-- Stock Alerts --}}
            <div class="analysis-card">
                <h3>Peringatan Stok</h3>
                @if ($stockStatus['habis'] > 0)
                    <div class="analysis-item" style="color: #dc3545;">
                        <span>Stok Habis:</span>
                        <span>{{ $stockStatus['habis'] }} item</span>
                    </div>
                @endif
                @if ($stockStatus['menipis'] > 0)
                    <div class="analysis-item" style="color: #ffc107;">
                        <span>Stok Menipis:</span>
                        <span>{{ $stockStatus['menipis'] }} item</span>
                    </div>
                @endif
                <div class="analysis-item" style="color: #28a745;">
                    <span>Stok Aman:</span>
                    <span>{{ $stockStatus['aman'] }} item</span>
                </div>
            </div>
        </div>
    @endif

    <div class="footer">
        <p>Laporan ini digenerate otomatis oleh sistem pada {{ now()->format('d M Y H:i:s') }}</p>
    </div>
</body>

</html>
