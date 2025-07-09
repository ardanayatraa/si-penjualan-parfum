<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Buku Besar</title>
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
            font-size: 9px;
            line-height: 1.3;
            color: #333;
            background: white;
        }

        .container {
            width: 100%;
            padding: 10px;
        }

        /* Header */
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 12px;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header .period {
            font-size: 11px;
            color: #666;
            margin-bottom: 5px;
        }

        .header .account-filter {
            font-size: 10px;
            color: #888;
            margin-bottom: 5px;
        }

        .header .print-info {
            font-size: 8px;
            color: #888;
        }

        /* Account Section */
        .account-section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .account-header {
            background-color: #f0f8ff;
            border: 2px solid #000;
            padding: 8px 12px;
            margin-bottom: 8px;
            display: table;
            width: 100%;
        }

        .account-info {
            display: table-cell;
            vertical-align: middle;
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
            display: table-cell;
            text-align: right;
            vertical-align: middle;
        }

        .account-balance .label {
            font-size: 8px;
            color: #666;
        }

        .account-balance .amount {
            font-size: 11px;
            font-weight: bold;
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

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 4px 6px;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background-color: #f5f5f5;
            font-weight: bold;
            font-size: 8px;
            text-transform: uppercase;
            page-break-inside: avoid;
            page-break-after: avoid;
        }

        td {
            font-size: 9px;
            page-break-inside: avoid;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        /* Column widths for landscape */
        .col-date {
            width: 10%;
        }

        .col-desc {
            width: 45%;
        }

        .col-debit {
            width: 15%;
        }

        .col-credit {
            width: 15%;
        }

        .col-balance {
            width: 15%;
        }

        /* Table rows */
        .data-row:nth-child(even) {
            background-color: #f9f9f9;
        }

        .total-row {
            background-color: #e6f3ff;
            font-weight: bold;
            border-top: 2px solid #000;
            page-break-inside: avoid;
        }

        /* Summary boxes */
        .summary-boxes {
            display: table;
            width: 100%;
            margin-top: 10px;
            border-spacing: 5px;
        }

        .summary-box {
            display: table-cell;
            width: 25%;
            border: 1px solid #ccc;
            padding: 6px;
            text-align: center;
            background-color: #f9f9f9;
        }

        .summary-box .label {
            font-size: 8px;
            color: #666;
            margin-bottom: 3px;
        }

        .summary-box .value {
            font-size: 10px;
            font-weight: bold;
        }

        /* No data */
        .no-data {
            text-align: center;
            padding: 15px;
            font-style: italic;
            color: #666;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
        }

        /* Page break utilities */
        .page-break {
            page-break-before: always;
        }

        .no-page-break {
            page-break-inside: avoid;
        }

        /* Overall summary */
        .overall-summary {
            margin-top: 20px;
            padding: 12px;
            border: 2px solid #000;
            background-color: #f8f9fa;
            page-break-inside: avoid;
        }

        .overall-summary h3 {
            font-size: 12px;
            margin-bottom: 10px;
            text-align: center;
            text-transform: uppercase;
        }

        .summary-stats {
            display: table;
            width: 100%;
            border-spacing: 8px;
        }

        .stat-item {
            display: table-cell;
            text-align: center;
            padding: 8px;
            border: 1px solid #ddd;
            background-color: white;
        }

        .stat-label {
            font-size: 8px;
            color: #666;
            margin-bottom: 3px;
        }

        .stat-value {
            font-size: 11px;
            font-weight: bold;
            color: #333;
        }

        /* Footer */
        .footer-notes {
            margin-top: 15px;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 10px;
            page-break-inside: avoid;
        }

        .footer-notes ul {
            margin-left: 15px;
            margin-top: 5px;
        }

        .footer-notes li {
            margin-bottom: 3px;
            line-height: 1.4;
        }

        /* Print optimization */
        @media print {
            .account-section {
                page-break-inside: avoid;
            }

            .account-header {
                page-break-after: avoid;
            }

            .total-row {
                page-break-before: avoid;
            }
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
                        Filter Akun: {{ $selectedAccount->kode_akun }} - {{ $selectedAccount->nama_akun }}
                    @endif
                </div>
            @endif
            <div class="print-info">
                Dicetak pada: {{ now()->format('d/m/Y H:i:s') }} |
                Total Akun: {{ count($data) }}
            </div>
        </div>

        @if (count($data) > 0)
            @foreach ($data as $akunId => $akunData)
                <div class="account-section no-page-break">
                    <!-- Account Header -->
                    <div class="account-header">
                        <div class="account-info">
                            <h3>{{ $akunData['akun']->kode_akun }} - {{ $akunData['akun']->nama_akun }}</h3>
                            <div class="account-type">
                                {{ ucfirst($akunData['akun']->tipe_akun) }} | {{ $akunData['akun']->kategori_akun }}
                            </div>
                        </div>
                        <div class="account-balance">
                            <div class="label">Saldo Awal:</div>
                            <div class="amount currency {{ $akunData['saldo_awal'] >= 0 ? 'positive' : 'negative' }}">
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
                                        // Hitung saldo berjalan berdasarkan tipe akun (sesuai seeder)
                                        if (in_array($tipeAkun, ['aset', 'beban'])) {
                                            $runningSaldo += $transaksi->debit - $transaksi->kredit;
                                        } else {
                                            $runningSaldo += $transaksi->kredit - $transaksi->debit;
                                        }
                                    @endphp
                                    <tr class="data-row">
                                        <td class="col-date">
                                            {{ \Carbon\Carbon::parse($transaksi->tanggal)->format('d/m/Y') }}
                                        </td>
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
                                <div class="value currency">{{ number_format($akunData['saldo_awal'], 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="summary-box">
                                <div class="label">Total Debit</div>
                                <div class="value currency positive">
                                    {{ number_format($akunData['total_debit'], 0, ',', '.') }}</div>
                            </div>
                            <div class="summary-box">
                                <div class="label">Total Kredit</div>
                                <div class="value currency negative">
                                    {{ number_format($akunData['total_kredit'], 0, ',', '.') }}</div>
                            </div>
                            <div class="summary-box">
                                <div class="label">Saldo Akhir</div>
                                <div
                                    class="value currency {{ $akunData['saldo_akhir'] >= 0 ? 'positive' : 'negative' }}">
                                    {{ number_format($akunData['saldo_akhir'], 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="no-data">
                            Tidak ada transaksi dalam periode {{ $startDate }} - {{ $endDate }}
                        </div>
                    @endif
                </div>

                @if (!$loop->last && count($akunData['transaksi']) > 10)
                    <div class="page-break"></div>
                @endif
            @endforeach

            <!-- Overall Summary -->
            @php
                $totalDebitKeseluruhan = collect($data)->sum('total_debit');
                $totalKreditKeseluruhan = collect($data)->sum('total_kredit');
                $jumlahTransaksi = collect($data)->sum(fn($item) => count($item['transaksi']));
                $jumlahAkunAktif = collect($data)->filter(fn($item) => count($item['transaksi']) > 0)->count();
            @endphp

            <div class="overall-summary">
                <h3>Ringkasan Keseluruhan</h3>
                <div class="summary-stats">
                    <div class="stat-item">
                        <div class="stat-label">Total Debit</div>
                        <div class="stat-value currency positive">Rp
                            {{ number_format($totalDebitKeseluruhan, 0, ',', '.') }}</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Total Kredit</div>
                        <div class="stat-value currency negative">Rp
                            {{ number_format($totalKreditKeseluruhan, 0, ',', '.') }}</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Akun Aktif</div>
                        <div class="stat-value">{{ $jumlahAkunAktif }} dari {{ count($data) }}</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Total Transaksi</div>
                        <div class="stat-value">{{ $jumlahTransaksi }}</div>
                    </div>
                </div>
            </div>
        @else
            <div class="no-data">
                <p><strong>Tidak ada data transaksi yang ditemukan</strong></p>
                <p>Periode: {{ $startDate }} - {{ $endDate }}</p>
                @if ($selectedAkun !== 'all')
                    <p>Filter akun: {{ $akunList->find($selectedAkun)->nama_akun ?? 'Tidak ditemukan' }}</p>
                @endif
                <p>Silakan periksa filter periode dan akun yang dipilih</p>
            </div>
        @endif

        <!-- Footer Notes -->
        <div class="footer-notes">
            <p><strong>Catatan Penting:</strong></p>
            <ul>
                <li>Laporan ini menampilkan detail transaksi buku besar untuk periode {{ $startDate }} sampai
                    {{ $endDate }}</li>
                <li>Saldo awal dihitung berdasarkan saldo akun ditambah mutasi sebelum periode laporan</li>
                <li>Akun Aset dan Beban menggunakan saldo normal debit (bertambah di debit, berkurang di kredit)</li>
                <li>Akun Kewajiban dan Pendapatan menggunakan saldo normal kredit (bertambah di kredit, berkurang di
                    debit)</li>
                <li>Saldo berjalan menunjukkan posisi saldo setelah setiap transaksi secara kronologis</li>
                <li>Total debit dan kredit harus seimbang sesuai prinsip akuntansi double entry</li>
            </ul>
        </div>
    </div>
</body>

</html>
