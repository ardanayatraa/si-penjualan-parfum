<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <style>
        @page {
            size: A4 landscape;
            margin: 20px;
        }

        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        h2,
        p {
            text-align: center;
            margin: 0;
            padding: 2px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            page-break-inside: auto;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px;
            text-align: left;
        }

        th {
            background: #f3f3f3;
        }

        .page-break {
            page-break-after: always;
        }

        .summary {
            margin-top: 20px;
            font-weight: bold;
            text-align: right;
        }
    </style>
</head>

<body>

    <h2>Laporan Penjualan</h2>
    <p>Periode: {{ $start }} s.d. {{ $end }}</p>

    @php
        $totalSubtotal = 0;
        $totalBruto = 0;
        $totalHarga = 0;
    @endphp

    @foreach ($data->chunk(20) as $chunk)
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Kasir</th>
                    <th>Barang</th>
                    <th>Tanggal</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                    <th>Harga Pokok</th>
                    <th>Laba Bruto</th>
                    <th>Total Harga</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($chunk as $i => $item)
                    @php
                        $totalSubtotal += $item->subtotal;
                        $totalBruto += $item->laba_bruto;
                        $totalHarga += $item->total_harga;
                    @endphp
                    <tr>
                        <td>{{ $loop->parent->index * 20 + $i + 1 }}</td>
                        <td>{{ $item->kasir->username ?? '-' }}</td>
                        <td>{{ $item->barang->nama_barang }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal_transaksi)->format('d-m-Y') }}</td>
                        <td>{{ $item->jumlah_penjualan }}</td>
                        <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->harga_pokok, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->laba_bruto, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if (!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach

    <div class="summary">
        <p>Total Subtotal: Rp {{ number_format($totalSubtotal, 0, ',', '.') }}</p>
        <p>Total Laba Bruto: Rp {{ number_format($totalBruto, 0, ',', '.') }}</p>
        <p>Total Harga: Rp {{ number_format($totalHarga, 0, ',', '.') }}</p>
    </div>

</body>

</html>
