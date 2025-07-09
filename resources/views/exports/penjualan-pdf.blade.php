{{-- resources/views/exports/penjualan-pdf.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>
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

        .header .period {
            margin: 5px 0;
            font-size: 14px;
            font-weight: normal;
        }

        .header .export-info {
            margin: 5px 0;
            font-size: 11px;
            color: #666;
        }

        .statistics-section {
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }

        .stats-card {
            flex: 1;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .stats-card h3 {
            margin: 0 0 10px;
            font-size: 12px;
            color: #333;
            text-transform: uppercase;
        }

        .stats-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 10px;
        }

        .stats-item.highlight {
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
            padding: 5px;
            text-align: left;
        }

        th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: center;
            font-size: 9px;
        }

        td {
            font-size: 9px;
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
        <h1>Laporan Penjualan</h1>
        <div class="period">
            Periode: {{ $start }} - {{ $end }}
        </div>
        <div class="export-info">
            Jenis Export: {{ ucfirst($exportType) }} |
            Dicetak: {{ now()->format('d M Y H:i') }}
        </div>
    </div>

    @if ($data->isEmpty())
        <div class="no-data">
            <p>Tidak ada data penjualan untuk periode yang dipilih.</p>
        </div>
    @else
        {{-- Statistics Section --}}
        <div class="statistics-section">
            <div class="stats-card">
                <h3>Ringkasan Transaksi</h3>
                <div class="stats-item">
                    <span>Total Transaksi:</span>
                    <span>{{ $statistics['totalTransaksi'] }}</span>
                </div>
                <div class="stats-item">
                    <span>Total Item Terjual:</span>
                    <span>{{ number_format($statistics['totalJumlahTerjual'], 0, ',', '.') }}</span>
                </div>
                <div class="stats-item">
                    <span>Rata-rata per Transaksi:</span>
                    <span>Rp {{ number_format($statistics['rataRataPerTransaksi'], 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="stats-card">
                <h3>Ringkasan Keuangan</h3>
                <div class="stats-item">
                    <span>Total Subtotal:</span>
                    <span>Rp {{ number_format($statistics['totalSubtotal'], 0, ',', '.') }}</span>
                </div>
                <div class="stats-item">
                    <span>Total Pajak:</span>
                    <span>Rp {{ number_format($statistics['totalPajak'], 0, ',', '.') }}</span>
                </div>
                <div class="stats-item highlight">
                    <span>Total Penjualan:</span>
                    <span>Rp {{ number_format($statistics['totalHarga'], 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="stats-card">
                <h3>Analisis Profitabilitas</h3>
                <div class="stats-item">
                    <span>Total HPP:</span>
                    <span>Rp {{ number_format($statistics['totalHargaPokok'], 0, ',', '.') }}</span>
                </div>
                <div class="stats-item highlight">
                    <span>Total Laba Bruto:</span>
                    <span>Rp {{ number_format($statistics['totalLabaBruto'], 0, ',', '.') }}</span>
                </div>
                <div class="stats-item">
                    <span>Margin Laba:</span>
                    <span>{{ number_format($statistics['marginLaba'], 2) }}%</span>
                </div>
            </div>
        </div>

        {{-- Main Data Table --}}
        <table>
            <thead>
                <tr>
                    <th style="width: 4%;">No</th>
                    <th style="width: 8%;">Kode</th>
                    <th style="width: 10%;">Tanggal</th>
                    <th style="width: 8%;">Kasir</th>
                    <th style="width: 18%;">Barang</th>
                    <th style="width: 6%;">Qty</th>
                    <th style="width: 10%;">Subtotal</th>
                    <th style="width: 8%;">Pajak</th>
                    <th style="width: 10%;">Total</th>
                    <th style="width: 8%;">HPP</th>
                    <th style="width: 10%;">Laba</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $index => $item)
                    @php
                        $pajakAmount = 0;
                        if ($item->pajak) {
                            $pajakAmount = ($item->subtotal * $item->pajak->presentase) / 100;
                        }
                        $hpp = $item->harga_pokok * $item->jumlah_terjual;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">PNJ-{{ str_pad($item->id, 6, '0', STR_PAD_LEFT) }}</td>
                        <td class="text-center">{{ $item->tanggal_transaksi->format('d-m-Y') }}</td>
                        <td>{{ $item->kasir->username ?? '-' }}</td>
                        <td>{{ $item->barang->nama_barang ?? '-' }}</td>
                        <td class="text-center">{{ $item->jumlah_terjual }}</td>
                        <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($pajakAmount, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($hpp, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($item->laba_bruto, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="5" class="text-center"><strong>TOTAL</strong></td>
                    <td class="text-center">
                        <strong>{{ number_format($statistics['totalJumlahTerjual'], 0, ',', '.') }}</strong></td>
                    <td class="text-right"><strong>Rp
                            {{ number_format($statistics['totalSubtotal'], 0, ',', '.') }}</strong></td>
                    <td class="text-right"><strong>Rp
                            {{ number_format($statistics['totalPajak'], 0, ',', '.') }}</strong></td>
                    <td class="text-right"><strong>Rp
                            {{ number_format($statistics['totalHarga'], 0, ',', '.') }}</strong></td>
                    <td class="text-right"><strong>Rp
                            {{ number_format($statistics['totalHargaPokok'], 0, ',', '.') }}</strong></td>
                    <td class="text-right"><strong>Rp
                            {{ number_format($statistics['totalLabaBruto'], 0, ',', '.') }}</strong></td>
                </tr>
            </tfoot>
        </table>

        {{-- Analysis Section --}}
        <div class="analysis-section">
            {{-- Payment Method Analysis --}}
            <div class="analysis-card">
                <h3>Analisis Metode Pembayaran</h3>
                @foreach ($paymentMethodStats as $method => $stats)
                    @php
                        $methodNames = [
                            'cash' => 'Tunai',
                            'transfer' => 'Transfer Bank',
                            'debit_card' => 'Kartu Debit',
                            'credit_card' => 'Kartu Kredit',
                            'e_wallet' => 'E-Wallet',
                        ];
                    @endphp
                    <div class="analysis-item">
                        <span>{{ $methodNames[$method] ?? $method }}:</span>
                        <span>{{ $stats['count'] }} ({{ number_format($stats['percentage'], 1) }}%)</span>
                    </div>
                @endforeach
            </div>

            {{-- Kasir Performance --}}
            <div class="analysis-card">
                <h3>Performa Kasir</h3>
                @foreach ($kasirStats->take(5) as $kasir => $stats)
                    <div class="analysis-item">
                        <span>{{ $kasir }}:</span>
                        <span>{{ $stats['count'] }} transaksi</span>
                    </div>
                @endforeach
            </div>

            {{-- Top Products --}}
            <div class="analysis-card">
                <h3>Produk Terlaris</h3>
                @php
                    $topProducts = $data
                        ->groupBy('barang.nama_barang')
                        ->map(function ($group) {
                            return [
                                'qty' => $group->sum('jumlah_terjual'),
                                'revenue' => $group->sum('total_harga'),
                            ];
                        })
                        ->sortByDesc('qty')
                        ->take(5);
                @endphp
                @foreach ($topProducts as $product => $stats)
                    <div class="analysis-item">
                        <span>{{ $product }}:</span>
                        <span>{{ $stats['qty'] }} unit</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="footer">
        <p>Laporan ini digenerate otomatis oleh sistem pada {{ now()->format('d M Y H:i:s') }}</p>
    </div>
</body>

</html>
