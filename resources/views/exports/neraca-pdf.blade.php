<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <style>
        @page {
            size: A4 landscape;
            margin: 20px
        }

        body {
            font-family: sans-serif;
            font-size: 12px
        }

        h2,
        p {
            text-align: center;
            margin: 0;
            padding: 4px
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px
        }

        th {
            background: #f3f3f3
        }

        .right {
            text-align: right
        }
    </style>
</head>

<body>

    <h2>NERACA</h2>
    <p>
        @if ($start && $end)
            Periode: {{ \Carbon\Carbon::parse($start)->format('d-m-Y') }}
            s.d. {{ \Carbon\Carbon::parse($end)->format('d-m-Y') }}
        @else
            Dicetak: {{ now()->format('d-m-Y H:i') }}
        @endif
    </p>

    <table>
        <thead>
            <tr>
                <th>No Akun</th>
                <th>Nama Akun</th>
                <th class="right">Debet</th>
                <th class="right">Kredit</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($list as $r)
                <tr>
                    <td>{{ $r['kode'] }}</td>
                    <td>{{ $r['nama'] }}</td>
                    <td class="right">{{ $r['debet'] > 0 ? 'Rp ' . number_format($r['debet'], 0, ',', '.') : '-' }}</td>
                    <td class="right">{{ $r['kredit'] > 0 ? 'Rp ' . number_format($r['kredit'], 0, ',', '.') : '-' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2">TOTAL</th>
                <th class="right">Rp {{ number_format($totalDebet, 0, ',', '.') }}</th>
                <th class="right">Rp {{ number_format($totalKredit, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

</body>

</html>
