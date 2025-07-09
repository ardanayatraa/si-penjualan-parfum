{{-- resources/views/exports/barang-pdf.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Data Barang</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 20px;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
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

        .summary {
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }

        .summary-card {
            flex: 1;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .summary-card h3 {
            margin: 0 0 10px;
            font-size: 12px;
            color: #333;
            text-transform: uppercase;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 10px;
        }

        .summary-item.highlight {
            font-weight: bold;
            font-size: 11px;
            color: #2c5aa0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: center;
            font-size: 10px;
        }

        td {
            font-size: 10px;
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
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            color: white;
        }

        .stock-aman {
            background-color: #28a745;
        }

        .stock-menipis {
            background-color: #ffc107;
            color: #000;
        }

        .stock-habis {
            background-color: #dc3545;
        }

        .margin-positive {
            color: #28a745;
            font-weight: bold;
        }

        .margin-negative {
            color: #dc3545;
            font-weight: bold;
        }

        .footer {
            position: fixed;
            bottom: 20px;
            width: 100%;
            text-align: center;
            font-size: 8px;
            color: #666;
        }

        .analysis-section {
            margin-top: 25px;
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
            font-size: 12px;
            color: #333;
            text-transform: uppercase;
        }

        .analysis-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 10px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Data Barang</h1>
        <div class="export-info">
            Diekspor: {{ now()->format('d M Y H:i') }} |
            Total: {{ $barangs->count() }} barang
        </div>
    </div>

    @php
        $totalItems = $barangs->count();
        $totalStok = $barangs->sum('stok');
        $totalNilaiStok = $barangs->sum(fn($item) => $item->stok * $item->harga_beli);
        $rataRataNilai = $totalItems > 0 ? $totalNilaiStok / $totalItems : 0;

        $stokAman = $barangs->where('stok', '>', 10)->count();
        $stokMenupis = $barangs->where('stok', '>', 0)->where('stok', '<=', 10)->count();
        $stokHabis = $barangs->where('stok', 0)->count();

        $supplierStats = $barangs->groupBy('supplier.nama_supplier');
    @endphp

    {{-- Summary Section --}}
    <div class="summary">
        <div class="summary-card">
            <h3>Ringkasan Barang</h3>
            <div class="summary-item">
                <span>Total Barang:</span>
                <span>{{ number_format($totalItems, 0, ',', '.') }}</span>
            </div>
            <div class="summary-item">
                <span>Total Stok:</span>
                <span>{{ number_format($totalStok, 0, ',', '.') }}</span>
            </div>
            <div class="summary-item highlight">
                <span>Total Nilai Stok:</span>
                <span>Rp {{ number_format($totalNilaiStok, 0, ',', '.') }}</span>
            </div>
            <div class="summary-item">
                <span>Rata-rata Nilai:</span>
                <span>Rp {{ number_format($rataRataNilai, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="summary-card">
            <h3>Status Stok</h3>
            <div class="summary-item">
                <span>Stok Aman (> 10):</span>
                <span style="color: #28a745; font-weight: bold;">{{ $stokAman }}</span>
            </div>
            <div class="summary-item">
                <span>Stok Menipis (1-10):</span>
                <span style="color: #ffc107; font-weight: bold;">{{ $stokMenupis }}</span>
            </div>
            <div class="summary-item">
                <span>Stok Habis (0):</span>
                <span style="color: #dc3545; font-weight: bold;">{{ $stokHabis }}</span>
            </div>
            <div class="summary-item">
                <span>Persentase Stok Aman:</span>
                <span>{{ $totalItems > 0 ? number_format(($stokAman / $totalItems) * 100, 1) : 0 }}%</span>
            </div>
        </div>

        <div class="summary-card">
            <h3>Distribusi Supplier</h3>
            @foreach ($supplierStats->take(4) as $supplier => $items)
                <div class="summary-item">
                    <span>{{ $supplier ?: 'Tidak ada supplier' }}:</span>
                    <span>{{ $items->count() }} barang</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Main Data Table --}}
    <table>
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 15%;">Supplier</th>
                <th style="width: 20%;">Nama Barang</th>
                <th style="width: 8%;">Satuan</th>
                <th style="width: 10%;">Harga Beli</th>
                <th style="width: 10%;">Harga Jual</th>
                <th style="width: 7%;">Margin</th>
                <th style="width: 8%;">Stok</th>
                <th style="width: 8%;">Status</th>
                <th style="width: 12%;">Nilai Stok</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($barangs as $index => $barang)
                @php
                    $nilaiStok = $barang->stok * $barang->harga_beli;
                    $margin =
                        $barang->harga_beli > 0
                            ? (($barang->harga_jual - $barang->harga_beli) / $barang->harga_beli) * 100
                            : 0;
                    $statusStok = match (true) {
                        $barang->stok == 0 => 'habis',
                        $barang->stok <= 10 => 'menipis',
                        default => 'aman',
                    };
                    $marginClass = $margin > 0 ? 'margin-positive' : 'margin-negative';
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $barang->supplier->nama_supplier ?? '-' }}</td>
                    <td>{{ $barang->nama_barang }}</td>
                    <td class="text-center">{{ $barang->satuan ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($barang->harga_beli, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($barang->harga_jual, 0, ',', '.') }}</td>
                    <td class="text-center {{ $marginClass }}">{{ number_format($margin, 1) }}%</td>
                    <td class="text-center">{{ $barang->stok }}</td>
                    <td class="text-center">
                        <span class="stock-status stock-{{ $statusStok }}">
                            {{ strtoupper($statusStok) }}
                        </span>
                    </td>
                    <td class="text-right">Rp {{ number_format($nilaiStok, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="7" class="text-center"><strong>TOTAL</strong></td>
                <td class="text-center"><strong>{{ number_format($totalStok, 0, ',', '.') }}</strong></td>
                <td></td>
                <td class="text-right"><strong>Rp {{ number_format($totalNilaiStok, 0, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>

    {{-- Analysis Section --}}
    <div class="analysis-section">
        <div class="analysis-card">
            <h3>Top 5 Barang Nilai Stok Tertinggi</h3>
            @foreach ($barangs->sortByDesc(fn($item) => $item->stok * $item->harga_beli)->take(5) as $item)
                @php
                    $nilaiStok = $item->stok * $item->harga_beli;
                @endphp
                <div class="analysis-item">
                    <span>{{ $item->nama_barang }}:</span>
                    <span>Rp {{ number_format($nilaiStok, 0, ',', '.') }}</span>
                </div>
            @endforeach
        </div>

        <div class="analysis-card">
            <h3>Margin Tertinggi</h3>
            @foreach ($barangs->sortByDesc(fn($item) => $item->harga_beli > 0 ? (($item->harga_jual - $item->harga_beli) / $item->harga_beli) * 100 : 0)->take(5) as $item)
                @php
                    $margin =
                        $item->harga_beli > 0 ? (($item->harga_jual - $item->harga_beli) / $item->harga_beli) * 100 : 0;
                @endphp
                <div class="analysis-item">
                    <span>{{ $item->nama_barang }}:</span>
                    <span>{{ number_format($margin, 1) }}%</span>
                </div>
            @endforeach
        </div>

        <div class="analysis-card">
            <h3>Peringatan Stok</h3>
            @if ($stokHabis > 0)
                <div class="analysis-item" style="color: #dc3545;">
                    <span>⚠️ Stok Habis:</span>
                    <span>{{ $stokHabis }} barang</span>
                </div>
            @endif
            @if ($stokMenupis > 0)
                <div class="analysis-item" style="color: #ffc107;">
                    <span>⚠️ Stok Menipis:</span>
                    <span>{{ $stokMenupis }} barang</span>
                </div>
            @endif
            <div class="analysis-item" style="color: #28a745;">
                <span>✅ Stok Aman:</span>
                <span>{{ $stokAman }} barang</span>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Data Barang digenerate otomatis oleh sistem pada {{ now()->format('d M Y H:i:s') }}</p>
    </div>
</body>

</html>
