<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Arus Kas</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 15mm 20mm 15mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.3;
            color: #333;
            background: white;
        }

        /* Header */
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 12px;
            margin-bottom: 20px;
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

        /* Section Headers */
        .section-header {
            font-size: 14px;
            font-weight: bold;
            margin: 20px 0 12px 0;
            padding: 8px 12px;
            border: 1px solid #000;
            text-align: center;
            text-transform: uppercase;
            page-break-inside: avoid;
        }

        .section-header.income {
            background-color: #e8f5e8;
            color: #2d7a2d;
            border-color: #2d7a2d;
        }

        .section-header.expense {
            background-color: #ffeaea;
            color: #c53030;
            border-color: #c53030;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            page-break-inside: auto;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 8px 10px;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background-color: #f5f5f5;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            page-break-inside: avoid;
            page-break-after: avoid;
        }

        td {
            font-size: 11px;
            page-break-inside: avoid;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        /* Table rows */
        .data-row:nth-child(even) {
            background-color: #f9f9f9;
        }

        .total-row {
            background-color: #e6f3ff;
            font-weight: bold;
            font-size: 12px;
            page-break-inside: avoid;
        }

        .total-row.income {
            background-color: #e8f5e8;
            color: #2d7a2d;
        }

        .total-row.expense {
            background-color: #ffeaea;
            color: #c53030;
        }

        /* Summary Section */
        .summary {
            margin-top: 25px;
            border: 2px solid #000;
            padding: 15px;
            background-color: #f9f9f9;
            page-break-inside: avoid;
        }

        .summary h2 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 15px;
            text-align: center;
            text-transform: uppercase;
            border-bottom: 1px solid #666;
            padding-bottom: 8px;
        }

        .summary-table {
            width: 100%;
            border: none;
            margin: 0;
        }

        .summary-table td {
            border: none;
            border-bottom: 1px dotted #ccc;
            padding: 8px 5px;
            font-size: 11px;
        }

        .summary-table .summary-label {
            font-weight: bold;
            width: 60%;
        }

        .summary-table .summary-value {
            font-weight: bold;
            text-align: right;
            width: 40%;
            font-family: 'DejaVu Sans Mono', monospace;
        }

        .summary-table .final-total {
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            font-size: 13px;
            font-weight: bold;
            background-color: #e6f3ff;
            padding: 12px 5px;
        }

        /* Colors */
        .positive {
            color: #2d7a2d;
        }

        .negative {
            color: #c53030;
        }

        .currency {
            font-family: 'DejaVu Sans Mono', monospace;
        }

        /* Additional Info */
        .additional-info {
            margin-top: 20px;
            padding: 12px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 3px;
            font-size: 10px;
            page-break-inside: avoid;
        }

        .additional-info h4 {
            font-size: 11px;
            margin-bottom: 8px;
            color: #555;
        }

        .additional-info ul {
            margin-left: 15px;
            margin-top: 5px;
        }

        .additional-info li {
            margin-bottom: 3px;
            line-height: 1.4;
        }

        /* Statistics */
        .statistics {
            margin-top: 15px;
            display: table;
            width: 100%;
            page-break-inside: avoid;
        }

        .stat-item {
            display: table-cell;
            width: 50%;
            padding: 8px;
            border: 1px solid #ddd;
            background-color: #f8f9fa;
            text-align: center;
            font-size: 10px;
        }

        .stat-value {
            font-weight: bold;
            font-size: 12px;
            color: #333;
            margin-top: 3px;
        }

        /* Page break utilities */
        .page-break {
            page-break-before: always;
        }

        .no-break {
            page-break-inside: avoid;
        }

        /* Footer content */
        .footer-content {
            position: fixed;
            bottom: 10mm;
            left: 15mm;
            right: 15mm;
            height: 15mm;
            border-top: 1px solid #ccc;
            padding-top: 5mm;
            font-size: 9px;
            color: #666;
        }

        .footer-left {
            float: left;
        }

        .footer-right {
            float: right;
        }

        /* Responsive adjustments for different content lengths */
        @media print {
            .summary {
                page-break-inside: avoid;
            }

            .section-header {
                page-break-after: avoid;
            }

            .total-row {
                page-break-before: avoid;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <h1>Laporan Arus Kas</h1>
        <div class="period">
            Periode: {{ $startDate }} - {{ $endDate }}
        </div>
        <div class="print-info">
            Dicetak pada: {{ now()->format('d/m/Y H:i:s') }} |
            Periode: {{ $data['metadata']['periode']['jumlah_hari'] }} hari
        </div>
    </div>

    <!-- Kas Masuk Section -->
    <div class="section-header income">Arus Kas Masuk (Operasional)</div>
    <table>
        <thead>
            <tr>
                <th style="width: 10%;">No</th>
                <th style="width: 50%;">Keterangan</th>
                <th style="width: 20%;">Kategori</th>
                <th style="width: 20%;" class="text-right">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['kasMasuk'] as $index => $item)
                <tr class="data-row">
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item['keterangan'] }}</td>
                    <td class="text-center">{{ ucfirst($item['kategori']) }}</td>
                    <td class="text-right currency">{{ number_format($item['jumlah'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row income">
                <td colspan="3"><strong>Total Kas Masuk</strong></td>
                <td class="text-right currency">
                    <strong>{{ number_format($data['totals']['totalKasMasuk'], 0, ',', '.') }}</strong>
                </td>
            </tr>
        </tfoot>
    </table>

    <!-- Kas Keluar Section -->
    <div class="section-header expense">Arus Kas Keluar</div>
    <table>
        <thead>
            <tr>
                <th style="width: 10%;">No</th>
                <th style="width: 50%;">Keterangan</th>
                <th style="width: 20%;">Kategori</th>
                <th style="width: 20%;" class="text-right">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['kasKeluar'] as $index => $item)
                <tr class="data-row">
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item['keterangan'] }}</td>
                    <td class="text-center">{{ ucfirst($item['kategori']) }}</td>
                    <td class="text-right currency">{{ number_format($item['jumlah'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row expense">
                <td colspan="3"><strong>Total Kas Keluar</strong></td>
                <td class="text-right currency">
                    <strong>{{ number_format($data['totals']['totalKasKeluar'], 0, ',', '.') }}</strong>
                </td>
            </tr>
        </tfoot>
    </table>

    <!-- Summary Section -->
    <div class="summary no-break">
        <h2>Ringkasan Arus Kas</h2>

        <table class="summary-table">
            <tr>
                <td class="summary-label">Saldo Kas Awal Periode:</td>
                <td class="summary-value currency">Rp {{ number_format($data['totals']['saldoKasAwal'], 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td class="summary-label">Total Kas Masuk:</td>
                <td class="summary-value currency positive">Rp
                    {{ number_format($data['totals']['totalKasMasuk'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="summary-label">Total Kas Keluar:</td>
                <td class="summary-value currency negative">Rp
                    {{ number_format($data['totals']['totalKasKeluar'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="summary-label">Arus Kas Bersih:</td>
                <td
                    class="summary-value currency {{ $data['totals']['arusKasBersih'] >= 0 ? 'positive' : 'negative' }}">
                    Rp {{ number_format($data['totals']['arusKasBersih'], 0, ',', '.') }}
                </td>
            </tr>
            <tr class="final-total">
                <td class="summary-label">Saldo Kas Akhir Periode:</td>
                <td
                    class="summary-value currency {{ $data['totals']['saldoKasAkhir'] >= 0 ? 'positive' : 'negative' }}">
                    Rp {{ number_format($data['totals']['saldoKasAkhir'], 0, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>

    <!-- Statistics -->
    <div class="statistics no-break">
        <div class="stat-item">
            <div>Rata-rata Kas Masuk Harian</div>
            <div class="stat-value currency">Rp
                {{ number_format($data['metadata']['statistik']['rata_rata_kas_masuk_harian'], 0, ',', '.') }}</div>
        </div>
        <div class="stat-item">
            <div>Rata-rata Kas Keluar Harian</div>
            <div class="stat-value currency">Rp
                {{ number_format($data['metadata']['statistik']['rata_rata_kas_keluar_harian'], 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Additional Info -->
    <div class="additional-info no-break">
        <h4>Catatan Penting:</h4>
        <ul>
            <li>Laporan ini menunjukkan arus kas aktual berdasarkan transaksi yang telah selesai dalam periode
                {{ $startDate }} - {{ $endDate }}</li>
            <li>Saldo kas awal dihitung dari akun Kas (1.1.01) berdasarkan mutasi sampai akhir periode sebelumnya</li>
            <li>Penjualan tunai dan non-tunai termasuk dalam aktivitas operasional</li>
            <li>Pembayaran hutang dikategorikan sebagai aktivitas pendanaan</li>
            <li>Arus kas bersih positif menunjukkan surplus kas, negatif menunjukkan defisit kas</li>
            <li>Periode laporan: {{ $data['metadata']['periode']['jumlah_hari'] }} hari kerja</li>
        </ul>
    </div>

    <!-- Footer -->
    <div class="footer-content">
        <div class="footer-left">
            Laporan Arus Kas - {{ now()->format('d/m/Y H:i') }}
        </div>
        <div class="footer-right">
            Periode: {{ $rawStartDate }} s.d {{ $rawEndDate }}
        </div>
    </div>
</body>

</html>
