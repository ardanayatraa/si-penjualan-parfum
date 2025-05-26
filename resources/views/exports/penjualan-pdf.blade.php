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

    <h2>Laporan Return Barang</h2>
    <p>Periode: {{ $start_date }} s.d. {{ $end_date }}</p>

    @php
        $totalReturn = 0;
    @endphp

    @foreach ($data->chunk(20) as $chunk)
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Barang</th>
                    <th>Supplier</th>
                    <th>Tanggal Return</th>
                    <th>Jumlah Return</th>
                    <th>Alasan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($chunk as $item)
                    @php
                        $totalReturn += $item->jumlah;
                    @endphp
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->barang->nama_barang ?? '-' }}</td>
                        <td>{{ $item->supplier->nama ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal_return)->format('d-m-Y') }}</td>
                        <td>{{ $item->jumlah }}</td>
                        <td>{{ $item->alasan }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if (!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach

    <div class="summary">
        <p>Total Jumlah Return: {{ $totalReturn }}</p>
    </div>

</body>

</html>
