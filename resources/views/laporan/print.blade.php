<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead tr {
            background-color: #f2f2f2;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 6px;
            text-align: left;
        }
    </style>
</head>

<body>
    <h3>Laporan Penjualan</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>ID Kasir</th>
                <th>ID Barang</th>
                <th>Tanggal</th>
                <th>Jumlah</th>
                <th>Harga Jual</th>
                <th>Total Harga</th>
                <th>Total Transaksi</th>
                <th>Laba Bruto</th>
                <th>Laba Bersih</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $d)
                <tr>
                    <td>{{ $d->id }}</td>
                    <td>{{ $d->id_kasir }}</td>
                    <td>{{ $d->id_barang }}</td>
                    <td>{{ $d->tanggal_transaksi }}</td>
                    <td>{{ $d->jumlah }}</td>
                    <td>Rp {{ number_format($d->harga_jual, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($d->total_harga, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($d->total_nilai_transaksi, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($d->laba_bruto, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($d->laba_bersih, 0, ',', '.') }}</td>
                    <td>{{ $d->keterangan }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
