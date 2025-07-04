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

        h2 {
            text-align: center;
            margin-bottom: 10px;
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
    <h2>Laporan Stok & Transaksi Barang</h2>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nama Barang</th>
                <th>Satuan</th>
                <th>Harga Beli</th>
                <th>Harga Jual</th>
                <th>Stok</th>
                <th>Jumlah Beli</th>
                <th>Total Beli</th>
                <th>Jumlah Jual</th>
                <th>Total Jual</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->satuan }}</td>
                    <td>Rp {{ number_format($item->harga_beli, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                    <td>{{ $item->stok }}</td>
                    <td>{{ $item->qty_pembelian ?? 0 }}</td>
                    <td>Rp {{ number_format($item->nilai_pembelian ?? 0, 0, ',', '.') }}</td>
                    <td>{{ $item->qty_penjualan ?? 0 }}</td>
                    <td>Rp {{ number_format($item->nilai_penjualan ?? 0, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <p>Total Stok: {{ $totalStok }}</p>
        <p>Total Nilai Stok: Rp {{ number_format($totalNilaiStok, 0, ',', '.') }}</p>
        <p>Total Harga Pembelian: Rp {{ number_format($totalNilaiBeli, 0, ',', '.') }}</p>
        <p>Total Harga Penjualan: Rp {{ number_format($totalNilaiJual, 0, ',', '.') }}</p>
    </div>
</body>

</html>
