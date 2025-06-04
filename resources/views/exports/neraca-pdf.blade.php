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

        .summary {
            margin-top: 20px;
            font-weight: bold;
            text-align: right;
        }
    </style>
</head>

<body>
    <h2>Laporan Neraca</h2>
    <p>Tanggal: {{ $tanggal }}</p>

    <table>
        <thead>
            <tr>
                <th style="width:60%;">Akun</th>
                <th style="width:20%;">Jenis</th>
                <th style="width:20%;">Nilai (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($baris as $row)
                <tr>
                    <td>{{ $row['akun'] }}</td>
                    <td>{{ $row['jenis'] }}</td>
                    <td style="text-align: right;">{{ number_format($row['nilai'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            @php
                $totalAktiva = collect($baris)->where('jenis', 'Aktiva')->sum('nilai');
                $totalPasiva = collect($baris)->where('jenis', 'Pasiva')->sum('nilai');
            @endphp
            <tr>
                <td colspan="2">Total Aktiva</td>
                <td style="text-align: right;">{{ number_format($totalAktiva, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="2">Total Pasiva</td>
                <td style="text-align: right;">{{ number_format($totalPasiva, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="summary">
        @if ($totalAktiva === $totalPasiva)
            <p>Neraca Balance ✔</p>
        @else
            <p>Neraca TIDAK Balance ❌</p>
        @endif
    </div>
</body>

</html>
