<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Laba Rugi</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 25px;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
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

        .section-header {
            background-color: #e8f4f8;
            font-weight: bold;
            font-size: 14px;
            padding: 8px;
            border: 1px solid #333;
            text-transform: uppercase;
        }

        .account-row {
            border-left: 1px solid #333;
            border-right: 1px solid #333;
            border-bottom: 1px solid #ddd;
        }

        .account-row td {
            padding: 6px 8px;
        }

        .account-code {
            width: 15%;
            text-align: center;
            font-weight: bold;
        }

        .account-name {
            width: 60%;
            text-align: left;
            padding-left: 20px;
        }

        .account-amount {
            width: 25%;
            text-align: right;
            font-family: 'Courier New', monospace;
        }

        .subtotal-row {
            background-color: #f5f5f5;
            font-weight: bold;
            border: 1px solid #333;
        }

        .subtotal-row td {
            padding: 8px;
        }

        .total-row {
            background-color: #d4edda;
            font-weight: bold;
            border: 2px solid #333;
        }

        .total-row td {
            padding: 10px 8px;
            font-size: 13px;
        }

        .loss {
            color: #dc3545;
        }

        .profit {
            color: #28a745;
        }

        .summary-section {
            margin-top: 30px;
            padding: 15px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .summary-section h3 {
            margin: 0 0 15px;
            font-size: 14px;
            text-align: center;
            text-transform: uppercase;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 3px 0;
            border-bottom: 1px dotted #ccc;
        }

        .summary-item:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 13px;
        }

        .no-data {
            text-align: center;
            font-style: italic;
            color: #666;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: flex-end;
        }

        .signature-box {
            text-align: center;
            width: 200px;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 60px;
            padding-top: 5px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Laporan Laba Rugi</h1>
        <div class="period">
            Periode: {{ $startDate }} s.d {{ $endDate }}
        </div>
        <div class="export-info">
            Jenis Export: {{ ucfirst($exportType) }} |
            Dicetak: {{ now()->format('d M Y H:i') }}
        </div>
    </div>

    @if ($pendapatanData->isEmpty() && $bebanData->isEmpty() && $pendapatanPenjualan == 0)
        <div class="no-data">
            <p>Tidak ada data untuk periode yang dipilih.</p>
        </div>
    @else
        <!-- PENDAPATAN -->
        <table>
            <tr class="section-header">
                <td colspan="3">PENDAPATAN</td>
            </tr>

            @if ($pendapatanPenjualan > 0)
                <tr class="account-row">
                    <td class="account-code">4001</td>
                    <td class="account-name">Pendapatan Penjualan</td>
                    <td class="account-amount">Rp {{ number_format($pendapatanPenjualan, 0, ',', '.') }}</td>
                </tr>
            @endif

            @foreach ($pendapatanData as $item)
                @if ($item['jumlah'] > 0)
                    <tr class="account-row">
                        <td class="account-code">{{ $item['kode'] }}</td>
                        <td class="account-name">{{ $item['nama'] }}</td>
                        <td class="account-amount">Rp {{ number_format($item['jumlah'], 0, ',', '.') }}</td>
                    </tr>
                @endif
            @endforeach

            <tr class="subtotal-row">
                <td colspan="2">TOTAL PENDAPATAN</td>
                <td class="account-amount">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
            </tr>
        </table>

        <!-- BEBAN -->
        <table>
            <tr class="section-header">
                <td colspan="3">BEBAN</td>
            </tr>

            @if ($hpp > 0)
                <tr class="account-row">
                    <td class="account-code">5001</td>
                    <td class="account-name">Harga Pokok Penjualan</td>
                    <td class="account-amount">Rp {{ number_format($hpp, 0, ',', '.') }}</td>
                </tr>
            @endif

            @if ($pengeluaranOperasional > 0)
                <tr class="account-row">
                    <td class="account-code">5002</td>
                    <td class="account-name">Beban Operasional</td>
                    <td class="account-amount">Rp {{ number_format($pengeluaranOperasional, 0, ',', '.') }}</td>
                </tr>
            @endif

            @foreach ($bebanData as $item)
                @if ($item['jumlah'] > 0)
                    <tr class="account-row">
                        <td class="account-code">{{ $item['kode'] }}</td>
                        <td class="account-name">{{ $item['nama'] }}</td>
                        <td class="account-amount">Rp {{ number_format($item['jumlah'], 0, ',', '.') }}</td>
                    </tr>
                @endif
            @endforeach

            <tr class="subtotal-row">
                <td colspan="2">TOTAL BEBAN</td>
                <td class="account-amount">Rp {{ number_format($totalBeban, 0, ',', '.') }}</td>
            </tr>
        </table>

        <!-- LABA/RUGI -->
        <table>
            <tr class="total-row">
                <td colspan="2" style="font-size: 14px;">
                    @if ($labaRugi >= 0)
                        <span class="profit">LABA BERSIH</span>
                    @else
                        <span class="loss">RUGI BERSIH</span>
                    @endif
                </td>
                <td class="account-amount" style="font-size: 14px;">
                    <span class="{{ $labaRugi >= 0 ? 'profit' : 'loss' }}">
                        Rp {{ number_format(abs($labaRugi), 0, ',', '.') }}
                    </span>
                </td>
            </tr>
        </table>

        <!-- RINGKASAN -->
        <div class="summary-section">
            <h3>Ringkasan Keuangan</h3>
            <div class="summary-item">
                <span>Total Pendapatan:</span>
                <span>Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</span>
            </div>
            <div class="summary-item">
                <span>Total Beban:</span>
                <span>Rp {{ number_format($totalBeban, 0, ',', '.') }}</span>
            </div>
            <div class="summary-item">
                <span>Margin Laba:</span>
                <span>{{ $totalPendapatan > 0 ? number_format(($labaRugi / $totalPendapatan) * 100, 2) : '0.00' }}%</span>
            </div>
            <div class="summary-item">
                <span>{{ $labaRugi >= 0 ? 'Laba Bersih:' : 'Rugi Bersih:' }}</span>
                <span class="{{ $labaRugi >= 0 ? 'profit' : 'loss' }}">
                    Rp {{ number_format(abs($labaRugi), 0, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- TANDA TANGAN -->
        <div class="signature-section">
            <div class="signature-box">
                <div style="margin-bottom: 60px;">Mengetahui,</div>
                <div class="signature-line">
                    Manajer Keuangan
                </div>
            </div>
        </div>
    @endif
</body>

</html>
