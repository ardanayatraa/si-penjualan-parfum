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
        }

        h2,
        p {
            text-align: center;
            margin: 0 0 10px;
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
        }

        th {
            background-color: #f0f0f0;
        }

        tfoot td {
            font-weight: bold;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    @php
        $grandTotal = 0;
    @endphp

    @foreach ($data->chunk(20) as $chunkIndex => $chunk)
        <h2>Laporan Pembelian</h2>
        <p>Periode: {{ $start_date }} - {{ $end_date }}</p>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Barang</th>
                    <th>Supplier</th>
                    <th>Tanggal</th>
                    <th>Jumlah Pembelian</th>
                    <th>Total (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($chunk as $i => $item)
                    @php
                        $grandTotal += $item->total;
                    @endphp
                    <tr>
                        <td>{{ $chunkIndex * 20 + $i + 1 }}</td>
                        <td>{{ $item->barang->nama_barang ?? '-' }}</td>
                        <td>{{ $item->barang->supplier->nama_supplier ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal_transaksi)->format('d-m-Y') }}</td>
                        <td>{{ $item->jumlah_pembelian }}</td>
                        <td>Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>

            @if ($loop->last)
                <tfoot>
                    <tr>
                        <td colspan="5">Grand Total</td>
                        <td>Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            @endif
        </table>

        @if (!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>

</html>
