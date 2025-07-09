<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Pembelian</title>
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
        }

        .header h2 {
            margin: 0 0 5px;
            font-size: 18px;
        }

        .header p {
            margin: 0 0 5px;
            font-size: 14px;
        }

        .export-info {
            margin: 5px 0;
            font-size: 11px;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            page-break-inside: auto;
        }

        th,
        td {
            border: 1px solid #444;
            padding: 6px;
            text-align: center;
            vertical-align: middle;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        td:nth-child(2),
        td:nth-child(3) {
            text-align: left;
        }

        td:nth-child(6) {
            text-align: right;
        }

        .total-row {
            background-color: #f9f9f9;
            font-weight: bold;
        }

        .grand-total-row {
            background-color: #e8f4f8;
            font-weight: bold;
        }

        .page-break {
            page-break-after: always;
        }

        .page-header {
            margin-bottom: 15px;
        }

        .summary-section {
            margin-top: 20px;
            padding: 10px;
            background-color: #f5f5f5;
            border: 1px solid #ddd;
        }

        .summary-section h3 {
            margin: 0 0 10px;
            font-size: 14px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .no-data {
            text-align: center;
            font-style: italic;
            color: #666;
        }
    </style>
</head>

<body>
    @php
        $grandTotal = 0;
        $totalItems = $data->count();
        $totalPembelian = $data->sum('jumlah_pembelian');
        $itemsPerPage = 20;
        $chunks = $data->chunk($itemsPerPage);
    @endphp

    @if ($data->isEmpty())
        <div class="header">
            <h2>Laporan Pembelian</h2>
            <p>Periode: {{ $start_date }} - {{ $end_date }}</p>
            <p class="export-info">Jenis Export: {{ ucfirst($export_type) }}</p>
        </div>
        <div class="no-data">
            <p>Tidak ada data pembelian untuk periode yang dipilih.</p>
        </div>
    @else
        @foreach ($chunks as $chunkIndex => $chunk)
            <div class="page-header">
                <div class="header">
                    <h2>Laporan Pembelian</h2>
                    <p>Periode: {{ $start_date }} - {{ $end_date }}</p>
                    <p class="export-info">
                        Jenis Export: {{ ucfirst($export_type) }} |
                        Halaman: {{ $chunkIndex + 1 }} dari {{ $chunks->count() }} |
                        Total Data: {{ $totalItems }} transaksi
                    </p>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th width="8%">No</th>
                        <th width="25%">Barang</th>
                        <th width="20%">Supplier</th>
                        <th width="12%">Tanggal</th>
                        <th width="15%">Jumlah Pembelian</th>
                        <th width="20%">Total (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @php $chunkTotal = 0; @endphp
                    @foreach ($chunk as $i => $item)
                        @php
                            $grandTotal += $item->total;
                            $chunkTotal += $item->total;
                        @endphp
                        <tr>
                            <td>{{ $chunkIndex * $itemsPerPage + $i + 1 }}</td>
                            <td>{{ $item->barang->nama_barang ?? '-' }}</td>
                            <td>{{ $item->barang->supplier->nama_supplier ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_transaksi)->format('d-m-Y') }}</td>
                            <td>{{ number_format($item->jumlah_pembelian, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach

                    <!-- Subtotal per halaman -->
                    @if ($chunks->count() > 1)
                        <tr class="total-row">
                            <td colspan="5">Subtotal Halaman {{ $chunkIndex + 1 }}</td>
                            <td>Rp {{ number_format($chunkTotal, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                </tbody>

                <!-- Grand Total hanya di halaman terakhir -->
                @if ($loop->last)
                    <tfoot>
                        <tr class="grand-total-row">
                            <td colspan="4"><strong>Total Keseluruhan</strong></td>
                            <td><strong>{{ number_format($totalPembelian, 0, ',', '.') }}</strong></td>
                            <td><strong>Rp {{ number_format($grandTotal, 0, ',', '.') }}</strong></td>
                        </tr>
                    </tfoot>
                @endif
            </table>

            <!-- Summary hanya di halaman terakhir -->
            @if ($loop->last)
                <div class="summary-section">
                    <h3>Ringkasan Laporan</h3>
                    <div class="summary-item">
                        <span>Total Transaksi:</span>
                        <span>{{ $totalItems }} transaksi</span>
                    </div>
                    <div class="summary-item">
                        <span>Total Jumlah Pembelian:</span>
                        <span>{{ number_format($totalPembelian, 0, ',', '.') }}</span>
                    </div>
                    <div class="summary-item">
                        <span>Total Nilai Pembelian:</span>
                        <span>Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="summary-item">
                        <span>Rata-rata per Transaksi:</span>
                        <span>Rp {{ number_format($grandTotal / $totalItems, 0, ',', '.') }}</span>
                    </div>
                    <div class="summary-item">
                        <span>Tanggal Cetak:</span>
                        <span>{{ now()->format('d M Y H:i') }}</span>
                    </div>
                </div>
            @endif

            <!-- Page break kecuali halaman terakhir -->
            @if (!$loop->last)
                <div class="page-break"></div>
            @endif
        @endforeach
    @endif
</body>

</html>
