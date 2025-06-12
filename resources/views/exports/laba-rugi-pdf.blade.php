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

        .section {
            background: #e5e7eb;
            font-weight: bold
        }
    </style>
</head>

<body>

    <h2>LAPORAN LABA RUGI</h2>
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
                <th class="right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            {{-- Pendapatan --}}
            <tr class="section">
                <td colspan="3">PENDAPATAN</td>
            </tr>
            @foreach ($rows->where('tipe', 'Pendapatan') as $r)
                <tr>
                    <td>{{ $r['kode'] }}</td>
                    <td>{{ $r['nama'] }}</td>
                    <td class="right">Rp {{ number_format($r['jumlah'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="2"><strong>Total Pendapatan</strong></td>
                <td class="right"><strong>Rp {{ number_format($totalPdpt, 0, ',', '.') }}</strong></td>
            </tr>

            {{-- Beban --}}
            <tr class="section">
                <td colspan="3">BEBAN</td>
            </tr>
            @foreach ($rows->where('tipe', 'Beban') as $r)
                <tr>
                    <td>{{ $r['kode'] }}</td>
                    <td>{{ $r['nama'] }}</td>
                    <td class="right">Rp {{ number_format($r['jumlah'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="2"><strong>Total Beban</strong></td>
                <td class="right"><strong>Rp {{ number_format($totalBeban, 0, ',', '.') }}</strong></td>
            </tr>

            {{-- Laba/Rugi --}}
            <tr class="section">
                <td colspan="2">LABA / RUGI BERSIH</td>
                <td class="right"><strong>Rp {{ number_format($labaRugi, 0, ',', '.') }}</strong></td>
            </tr>
        </tbody>
    </table>

</body>

</html>
