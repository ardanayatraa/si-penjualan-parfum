<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Neraca</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 20px;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h2 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header .period {
            margin: 5px 0;
            font-size: 12px;
        }

        .header .export-info {
            margin: 5px 0;
            font-size: 10px;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: top;
        }

        th {
            background: #f3f3f3;
            font-weight: bold;
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .left {
            text-align: left;
        }

        .total-row {
            background-color: #e8f4f8;
            font-weight: bold;
        }

        .section-header {
            background-color: #d4edda;
            font-weight: bold;
            text-transform: uppercase;
        }

        .aset-row {
            background-color: #f8f9fa;
        }

        .kewajiban-row {
            background-color: #fff3cd;
        }

        .no-data {
            text-align: center;
            font-style: italic;
            color: #666;
            padding: 10px;
        }

        .summary-section {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }

        .summary-box {
            width: 48%;
            padding: 10px;
            border: 1px solid #ddd;
            background-color: #f8f9fa;
        }

        .summary-box h4 {
            margin: 0 0 10px;
            text-align: center;
            text-transform: uppercase;
            font-size: 14px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            padding: 2px 0;
            border-bottom: 1px dotted #ccc;
        }

        .balance-check {
            margin-top: 15px;
            padding: 10px;
            border: 2px solid #28a745;
            background-color: #d4edda;
            text-align: center;
            font-weight: bold;
        }

        .balance-error {
            border-color: #dc3545;
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Laporan Neraca</h2>
        <div class="period">
            Periode: {{ $start }} s.d {{ $end }}
        </div>
        <div class="export-info">
            Jenis Export: {{ ucfirst($exportType) }} |
            Dicetak: {{ now()->format('d M Y H:i') }}
        </div>
    </div>

    @if ($list->isEmpty())
        <div class="no-data">
            <p>Tidak ada data untuk periode yang dipilih.</p>
        </div>
    @else
        <table>
            <thead>
                <tr>
                    <th style="width: 15%;">Kode Akun</th>
                    <th style="width: 45%;">Nama Akun</th>
                    <th style="width: 10%;">Tipe</th>
                    <th style="width: 15%;">Debet</th>
                    <th style="width: 15%;">Kredit</th>
                </tr>
            </thead>
            <tbody>
                <!-- ASET -->
                @if ($aset->isNotEmpty())
                    <tr class="section-header">
                        <td colspan="5" class="center">ASET</td>
                    </tr>
                    @foreach ($aset as $item)
                        <tr class="aset-row">
                            <td class="center">{{ $item['kode'] }}</td>
                            <td class="left">{{ $item['nama'] }}</td>
                            <td class="center">{{ ucfirst($item['tipe']) }}</td>
                            <td class="right">
                                {{ $item['debit'] > 0 ? 'Rp ' . number_format($item['debit'], 0, ',', '.') : '-' }}
                            </td>
                            <td class="right">
                                {{ $item['kredit'] > 0 ? 'Rp ' . number_format($item['kredit'], 0, ',', '.') : '-' }}
                            </td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="3" class="center"><strong>TOTAL ASET</strong></td>
                        <td class="right"><strong>Rp {{ number_format($aset->sum('debit'), 0, ',', '.') }}</strong>
                        </td>
                        <td class="right"><strong>Rp {{ number_format($aset->sum('kredit'), 0, ',', '.') }}</strong>
                        </td>
                    </tr>
                @endif

                <!-- KEWAJIBAN -->
                @if ($kewajiban->isNotEmpty())
                    <tr class="section-header">
                        <td colspan="5" class="center">KEWAJIBAN</td>
                    </tr>
                    @foreach ($kewajiban as $item)
                        <tr class="kewajiban-row">
                            <td class="center">{{ $item['kode'] }}</td>
                            <td class="left">{{ $item['nama'] }}</td>
                            <td class="center">{{ ucfirst($item['tipe']) }}</td>
                            <td class="right">
                                {{ $item['debit'] > 0 ? 'Rp ' . number_format($item['debit'], 0, ',', '.') : '-' }}
                            </td>
                            <td class="right">
                                {{ $item['kredit'] > 0 ? 'Rp ' . number_format($item['kredit'], 0, ',', '.') : '-' }}
                            </td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="3" class="center"><strong>TOTAL KEWAJIBAN</strong></td>
                        <td class="right"><strong>Rp
                                {{ number_format($kewajiban->sum('debit'), 0, ',', '.') }}</strong></td>
                        <td class="right"><strong>Rp
                                {{ number_format($kewajiban->sum('kredit'), 0, ',', '.') }}</strong></td>
                    </tr>
                @endif
            </tbody>

            <!-- GRAND TOTAL -->
            <tfoot>
                <tr class="total-row" style="border-top: 2px solid #333;">
                    <td colspan="3" class="center" style="font-size: 14px;"><strong>TOTAL KESELURUHAN</strong></td>
                    <td class="right" style="font-size: 14px;"><strong>Rp
                            {{ number_format($totalDebet, 0, ',', '.') }}</strong></td>
                    <td class="right" style="font-size: 14px;"><strong>Rp
                            {{ number_format($totalKredit, 0, ',', '.') }}</strong></td>
                </tr>
            </tfoot>
        </table>

        <!-- SUMMARY SECTION -->
        <div class="summary-section">
            <div class="summary-box">
                <h4>Ringkasan Aset</h4>
                <div class="summary-item">
                    <span>Total Debet Aset:</span>
                    <span>Rp {{ number_format($aset->sum('debit'), 0, ',', '.') }}</span>
                </div>
                <div class="summary-item">
                    <span>Total Kredit Aset:</span>
                    <span>Rp {{ number_format($aset->sum('kredit'), 0, ',', '.') }}</span>
                </div>
                <div class="summary-item">
                    <span>Saldo Bersih Aset:</span>
                    <span>Rp {{ number_format($totalAset, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="summary-box">
                <h4>Ringkasan Kewajiban</h4>
                <div class="summary-item">
                    <span>Total Debet Kewajiban:</span>
                    <span>Rp {{ number_format($kewajiban->sum('debit'), 0, ',', '.') }}</span>
                </div>
                <div class="summary-item">
                    <span>Total Kredit Kewajiban:</span>
                    <span>Rp {{ number_format($kewajiban->sum('kredit'), 0, ',', '.') }}</span>
                </div>
                <div class="summary-item">
                    <span>Saldo Bersih Kewajiban:</span>
                    <span>Rp {{ number_format($totalKewajiban, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- BALANCE CHECK -->
        @php
            $isBalanced = abs($totalDebet - $totalKredit) < 0.01; // Toleransi pembulatan
        @endphp

        <div class="balance-check {{ $isBalanced ? '' : 'balance-error' }}">
            @if ($isBalanced)
                ✓ NERACA SEIMBANG - Total Debet ({{ number_format($totalDebet, 0, ',', '.') }}) = Total Kredit
                ({{ number_format($totalKredit, 0, ',', '.') }})
            @else
                ✗ NERACA TIDAK SEIMBANG - Selisih: Rp {{ number_format(abs($totalDebet - $totalKredit), 0, ',', '.') }}
            @endif
        </div>

        <!-- DETAIL PERIODE -->
        @if (!empty($rawStartDate) || !empty($rawEndDate))
            <div
                style="margin-top: 15px; padding: 10px; background-color: #f8f9fa; border: 1px solid #ddd; font-size: 11px;">
                <strong>Detail Periode:</strong>
                <span>Dari: {{ $rawStartDate ?: 'Awal Tahun' }}</span> |
                <span>Sampai: {{ $rawEndDate ?: 'Hari Ini' }}</span> |
                <span>Total Akun: {{ $list->count() }}</span> |
                <span>Aset: {{ $aset->count() }}</span> |
                <span>Kewajiban: {{ $kewajiban->count() }}</span>
            </div>
        @endif
    @endif
</body>

</html>
