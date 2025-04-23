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
            background-color: #f3f3f3;
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
    <p>Periode: {{ $start_date }} s.d. {{ $end_date }}</p>

    @php
        $totalBruto = 0;
        $totalBersih = 0;
    @endphp

    @foreach ($data->chunk(20) as $chunk)
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kasir</th>
                    <th>Barang</th>
                    <th>Tanggal Transaksi</th>
                    <th>Jumlah</th>
                    <th>Harga Jual</th>
                    <th>Pajak</th>
                    <th>Total Harga</th>
                    <th>Total Nilai Transaksi</th>
                    <th>Laba Bruto</th>
                    <th>Laba Bersih</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($chunk as $item)
                    @php
                        $totalBruto += $item->laba_bruto;
                        $totalBersih += $item->laba_bersih;
                    @endphp
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->kasir->username ?? '-' }}</td>
                        <td>{{ $item->barang->nama_barang ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal_transaksi)->format('d-m-Y') }}</td>
                        <td>{{ $item->jumlah }}</td>
                        <td>Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->pajak->nilai_pajak ?? 0, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->total_nilai_transaksi, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->laba_bruto, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->laba_bersih, 0, ',', '.') }}</td>
                        <td>{{ $item->keterangan }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if (!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach

    <div class="summary">
        <p>Total Laba Bruto: Rp {{ number_format($totalBruto, 0, ',', '.') }}</p>
        <p>Total Laba Bersih: Rp {{ number_format($totalBersih, 0, ',', '.') }}</p>
    </div>

</body>

</html>
