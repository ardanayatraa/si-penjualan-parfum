<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Arus Kas</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
            background: white;
        }

        .container {
            width: 100%;
            max-width: 100%;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header */
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header .period {
            font-size: 11px;
            color: #666;
            margin-bottom: 3px;
        }

        .header .print-date {
            font-size: 8px;
            color: #888;
        }

        /* Tables */
        .table-container {
            width: 100%;
            margin-bottom: 20px;
        }

        .table-wrapper {
            width: 48%;
            float: left;
            margin-right: 4%;
        }

        .table-wrapper:last-child {
            margin-right: 0;
        }

        .table-title {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 8px;
            padding: 5px;
            text-align: center;
            border: 1px solid #000;
            background-color: #f0f0f0;
        }

        .table-title.income {
            color: #2d7a2d;
            border-color: #2d7a2d;
        }

        .table-title.expense {
            color: #c53030;
            border-color: #c53030;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #f5f5f5;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }

        td {
            font-size: 10px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        /* Footer totals */
        .total-row {
            background-color: #f8f8f8;
            font-weight: bold;
        }

        .total-row.income {
            color: #2d7a2d;
        }

        .total-row.expense {
            color: #c53030;
        }

        /* Summary Section */
        .summary {
            clear: both;
            margin-top: 30px;
            border: 2px solid #000;
            padding: 15px;
            background-color: #f9f9f9;
        }

        .summary h2 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 15px;
            text-align: center;
            text-transform: uppercase;
            border-bottom: 1px solid #666;
            padding-bottom: 5px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #ddd;
        }

        .summary-item:last-child {
            border-bottom: 2px solid #000;
            font-weight: bold;
            font-size: 12px;
            margin-top: 10px;
            padding-top: 15px;
        }

        .summary-label {
            font-weight: bold;
            flex: 1;
        }

        .summary-value {
            font-weight: bold;
            text-align: right;
            flex: 1;
        }

        .positive {
            color: #2d7a2d;
        }

        .negative {
            color: #c53030;
        }

        /* Footer */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 50px;
            border-top: 1px solid #ccc;
            padding: 10px 20px;
            font-size: 8px;
            color: #666;
        }

        .footer-left {
            float: left;
        }

        .footer-right {
            float: right;
        }

        /* Page break */
        .page-break {
            page-break-before: always;
        }

        /* Clearfix */
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }

        /* Number formatting */
        .currency {
            font-family: 'DejaVu Sans Mono', monospace;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Laporan Arus Kas</h1>
            <div class="period">
                Periode: {{ $startDate }} - {{ $endDate }}
            </div>
            <div class="print-date">
                Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}
            </div>
        </div>

        <!-- Tables Section -->
        <div class="table-container clearfix">
            <!-- Kas Masuk -->
            <div class="table-wrapper">
                <div class="table-title income">ARUS KAS MASUK</div>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 60%;">Keterangan</th>
                            <th style="width: 40%;" class="text-right">Jumlah (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data['kasMasuk'] as $item)
                            <tr>
                                <td>{{ $item['keterangan'] }}</td>
                                <td class="text-right currency">{{ number_format($item['jumlah'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="total-row income">
                            <td><strong>Total Kas Masuk</strong></td>
                            <td class="text-right currency">
                                <strong>{{ number_format($data['totals']['totalKasMasuk'], 0, ',', '.') }}</strong>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Kas Keluar -->
            <div class="table-wrapper">
                <div class="table-title expense">ARUS KAS KELUAR</div>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 60%;">Keterangan</th>
                            <th style="width: 40%;" class="text-right">Jumlah (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data['kasKeluar'] as $item)
                            <tr>
                                <td>{{ $item['keterangan'] }}</td>
                                <td class="text-right currency">{{ number_format($item['jumlah'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="total-row expense">
                            <td><strong>Total Kas Keluar</strong></td>
                            <td class="text-right currency">
                                <strong>{{ number_format($data['totals']['totalKasKeluar'], 0, ',', '.') }}</strong>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Summary Section -->
        <div class="summary">
            <h2>Ringkasan Arus Kas</h2>

            <div class="summary-item">
                <span class="summary-label">Total Kas Masuk:</span>
                <span class="summary-value currency positive">Rp
                    {{ number_format($data['totals']['totalKasMasuk'], 0, ',', '.') }}</span>
            </div>

            <div class="summary-item">
                <span class="summary-label">Total Kas Keluar:</span>
                <span class="summary-value currency negative">Rp
                    {{ number_format($data['totals']['totalKasKeluar'], 0, ',', '.') }}</span>
            </div>

            <div class="summary-item">
                <span class="summary-label">Arus Kas Bersih:</span>
                <span
                    class="summary-value currency {{ $data['totals']['arusKasBersih'] >= 0 ? 'positive' : 'negative' }}">
                    Rp {{ number_format($data['totals']['arusKasBersih'], 0, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- Additional Info -->
        <div style="margin-top: 30px; font-size: 9px; color: #666;">
            <p><strong>Catatan:</strong></p>
            <ul style="margin-left: 20px; margin-top: 5px;">
                <li>Laporan ini menunjukkan arus kas aktual berdasarkan transaksi yang telah selesai</li>
                <li>Penjualan tunai dan non-tunai termasuk dalam kas masuk operasional</li>
                <li>Pembelian dan pengeluaran operasional termasuk dalam kas keluar operasional</li>
                <li>Arus kas bersih positif menunjukkan surplus kas, negatif menunjukkan defisit kas</li>
            </ul>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-left">
            Laporan Arus Kas - {{ now()->format('d/m/Y') }}
        </div>
        <div class="footer-right">
            Halaman 1 dari 1
        </div>
    </div>
</body>

</html>
