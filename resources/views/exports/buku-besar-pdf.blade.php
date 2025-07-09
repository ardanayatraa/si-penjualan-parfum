<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Buku Besar</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9px;
            line-height: 1.3;
            color: #333;
            background: white;
        }

        .container {
            width: 100%;
            max-width: 100%;
            margin: 0 auto;
            padding: 15px;
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

        .header .account-filter {
            font-size: 10px;
            color: #888;
            margin-bottom: 3px;
        }

        .header .print-date {
            font-size: 8px;
            color: #888;
        }

        /* Account Section */
        .account-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }

        .account-header {
            background-color: #f0f0f0;
            border: 2px solid #000;
            padding: 8px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .account-info h3 {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .account-info .account-type {
            font-size: 9px;
            color: #666;
        }

        .account-balance {
            text-align: right;
        }

        .account-balance .label {
            font-size: 8px;
            color: #666;
        }

        .account-balance .amount {
            font-size: 11px;
            font-weight: bold;
        }

        .positive {
            color: #2d7a2d;
        }

        .negative {
            color: #c53030;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 4px 6px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #f5f5f5;
            font-weight: bold;
            font-size: 8px;
            text-transform: uppercase;
        }

        td {
            font-size: 9px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        /* Column widths */
        .col-date {
            width: 12%;
        }

        .col-desc {
            width: 40%;
        }

        .col-debit {
            width: 15%;
        }

        .col-credit {
            width: 15%;
        }

        .col-balance {
            width: 18%;
        }

        /* Footer totals */
        .total-row {
            background-color: #f8f8f8;
            font-weight: bold;
            border-top: 2px solid #000;
        }

        /* Summary boxes */
        .summary-boxes {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            gap: 10px;
        }

        .summary-box {
            flex: 1;
            border: 1px solid #ccc;
            padding: 6px;
            text-align: center;
            background-color: #f9f9f9;
        }

        .summary-box .label {
            font-size: 8px;
            color: #666;
            margin-bottom: 2px;
        }

        .summary-box .value {
            font-size: 10px;
            font-weight: bold;
        }

        /* No data message */
        .no-data {
            text-align: center;
            padding: 20px;
            font-style: italic;
            color: #666;
        }

        /* Footer */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 40px;
            border-top: 1px solid #ccc;
            padding: 8px 15px;
            font-size: 7px;
            color: #666;
            background: white;
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

        /* Currency formatting */
        .currency {
            font-family: 'DejaVu Sans Mono', monospace;
        }

        /* Empty row for spacing */
        .empty-row {
            height: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Laporan Buku Besar</h1>
            <div class="period">
                Periode: {{ $startDate }} - {{ $endDate }}
            </div>
            @if ($selectedAkun !== 'all')
                <div class="account-filter">
                    @php
                        $selectedAccount = $akunList->find($selectedAkun);
                    @endphp
                    @if ($selectedAccount)
                        Akun: {{ $selectedAccount->kode_akun }} - {{ $selectedAccount->nama_akun }}
                    @endif
                </div>
            @endif
            <div class="print-date">
                Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}
            </div>
        </div>

        @if (count($data) > 0)
            @foreach ($data as $akunId => $akunData)
                <div class="account-section">
                    <!-- Account Header -->
                    <div class="account-header">
                        <div class="account-info">
                            <h3>{{ $akunData['akun']->kode_akun }} - {{ $akunData['akun']->nama_akun }}</h3>
                            <div class="account-type">{{ $akunData['akun']->tipe_akun }} |
                                {{ $akunData['akun']->kategori_akun }}</div>
                        </div>
                        <div class="account-balance">
                            <div class="label">Saldo Awal:</div>
                            <div class="amount {{ $akunData['saldo_awal'] >= 0 ? 'positive' : 'negative' }}">
                                Rp {{ number_format($akunData['saldo_awal'], 0, ',', '.') }}
                            </div>
                        </div>
                    </div>

                    @if (count($akunData['transaksi']) > 0)
                        <!-- Transactions Table -->
                        <table>
                            <thead>
                                <tr>
                                    <th class="col-date">Tanggal</th>
                                    <th class="col-desc">Keterangan</th>
                                    <th class="col-debit text-right">Debit (Rp)</th>
                                    <th class="col-credit text-right">Kredit (Rp)</th>
                                    <th class="col-balance text-right">Saldo (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $runningSaldo = $akunData['saldo_awal'];
                                    $tipeAkun = $akunData['akun']->tipe_akun;
                                @endphp
                                @foreach ($akunData['transaksi'] as $transaksi)
                                    @php
                                        // Hitung saldo berjalan
                                        if (in_array($tipeAkun, ['Aset', 'Beban'])) {
                                            $runningSaldo += $transaksi->debit - $transaksi->kredit;
                                        } else {
                                            $runningSaldo += $transaksi->kredit - $transaksi->debit;
                                        }
                                    @endphp
                                    <tr>
                                        <td class="col-date">
                                            {{ \Carbon\Carbon::parse($transaksi->tanggal)->format('d/m/Y') }}</td>
                                        <td class="col-desc">{{ $transaksi->keterangan }}</td>
                                        <td class="col-debit text-right currency">
                                            @if ($transaksi->debit > 0)
                                                {{ number_format($transaksi->debit, 0, ',', '.') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="col-credit text-right currency">
                                            @if ($transaksi->kredit > 0)
                                                {{ number_format($transaksi->kredit, 0, ',', '.') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td
                                            class="col-balance text-right currency {{ $runningSaldo >= 0 ? 'positive' : 'negative' }}">
                                            {{ number_format($runningSaldo, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="total-row">
                                    <td colspan="2" class="text-right"><strong>Total:</strong></td>
                                    <td class="text-right currency">
                                        <strong>{{ number_format($akunData['total_debit'], 0, ',', '.') }}</strong>
                                    </td>
                                    <td class="text-right currency">
                                        <strong>{{ number_format($akunData['total_kredit'], 0, ',', '.') }}</strong>
                                    </td>
                                    <td
                                        class="text-right currency {{ $akunData['saldo_akhir'] >= 0 ? 'positive' : 'negative' }}">
                                        <strong>{{ number_format($akunData['saldo_akhir'], 0, ',', '.') }}</strong>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>

                        <!-- Summary Boxes -->
                        <div class="summary-boxes">
                            <div class="summary-box">
                                <div class="label">Saldo Awal</div>
                                <div class="value">{{ number_format($akunData['saldo_awal'], 0, ',', '.') }}</div>
                            </div>
                            <div class="summary-box">
                                <div class="label">Total Debit</div>
                                <div class="value positive">{{ number_format($akunData['total_debit'], 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="summary-box">
                                <div class="label">Total Kredit</div>
                                <div class="value negative">{{ number_format($akunData['total_kredit'], 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="summary-box">
                                <div class="label">Saldo Akhir</div>
                                <div class="value {{ $akunData['saldo_akhir'] >= 0 ? 'positive' : 'negative' }}">
                                    {{ number_format($akunData['saldo_akhir'], 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="no-data">
                            Tidak ada transaksi dalam periode ini
                        </div>
                    @endif
                </div>

                @if (!$loop->last)
                    <div class="empty-row"></div>
                @endif
            @endforeach
        @else
            <div class="no-data">
                <p><strong>Tidak ada data transaksi yang ditemukan</strong></p>
                <p>Silakan periksa filter periode dan akun yang dipilih</p>
            </div>
        @endif

        <!-- Summary Info -->
        <div style="margin-top: 30px; font-size: 8px; color: #666; border-top: 1px solid #ccc; padding-top: 10px;">
            <p><strong>Catatan:</strong></p>
            <ul style="margin-left: 15px; margin-top: 5px;">
                <li>Laporan ini menunjukkan detail transaksi untuk setiap akun dalam periode yang dipilih</li>
                <li>Saldo dihitung berdasarkan saldo awal ditambah/dikurangi transaksi debit dan kredit</li>
                <li>Akun Aset dan Beban memiliki saldo normal debit, akun Liabilitas, Ekuitas, dan Pendapatan memiliki
                    saldo normal kredit</li>
                <li>Saldo berjalan menunjukkan posisi saldo setelah setiap transaksi</li>
            </ul>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-left">
            Laporan Buku Besar - {{ now()->format('d/m/Y H:i:s') }}
        </div>
        <div class="footer-right">
            Halaman <span class="pageNumber"></span>
        </div>
    </div>

    <!-- Page numbering script -->
    <script>
        // This would work in a real browser environment
        // For DomPDF, page numbers need to be handled differently
    </script>
</body>

</html>
