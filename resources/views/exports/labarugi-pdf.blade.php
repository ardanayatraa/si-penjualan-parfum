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
    <h2>Laporan Laba Rugi</h2>
    <p>Periode: {{ $start }} s.d. {{ $end }}</p>

    @php
        $totalPenjualan = 0;
        $totalPembelian = 0;
        $totalLabaRugi = 0;
    @endphp

    @foreach ($data->chunk(20) as $chunk)
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tanggal</th>
                    <th>Total Penjualan</th>
                    <th>Total Pembelian</th>
                    <th>Laba/Rugi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($chunk as $i => $item)
                    @php
                        $totalPenjualan += $item->total_penjualan;
                        $totalPembelian += $item->total_pembelian;
                        $totalLabaRugi += $item->laba_rugi;
                    @endphp
                    <tr>
                        <td>{{ $loop->parent->index * 20 + $i + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tgl)->format('d-m-Y') }}</td>
                        <td>Rp {{ number_format($item->total_penjualan, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->total_pembelian, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->laba_rugi, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if (!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach

    <div class="summary">
        <p>Total Penjualan: Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</p>
        <p>Total Pembelian: Rp {{ number_format($totalPembelian, 0, ',', '.') }}</p>
        <p>Total Laba/Rugi: Rp {{ number_format($totalLabaRugi, 0, ',', '.') }}</p>
    </div>
</body>

</html>
