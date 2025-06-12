<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: monospace;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }

        .wrapper {
            margin: 0 8px;
            /* tipis kiri & kanan */
            padding: 10px 0;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        hr {
            border: none;
            border-top: 1px dashed #333;
            margin: 4px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 4px 0;
        }

        td {
            padding: 2px 0;
        }

        .header .title {
            font-size: 14px;
            font-weight: bold;
        }

        .header .subtitle {
            font-size: 10px;
            color: #555;
        }

        .total-row span {
            font-size: 13px;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="header center">
            <div class="title">SI PARFUM</div>
            <div class="subtitle">Jl. Contoh No.123, Jakarta</div>
            <div class="subtitle">Telp. (021) 1234567</div>
        </div>
        <hr />
        <div>
            <div>No Nota: <strong>PNJ-{{ $transaksi->id }}</strong></div>
            <div>Tanggal: {{ $transaksi->tanggal_transaksi->format('d-m-Y H:i') }}</div>
            <div>Kasir: {{ $transaksi->kasir->username ?? '-' }}</div>
        </div>
        <hr />
        <table>
            <tr>
                <td><strong>Barang</strong></td>
                <td class="right">{{ $transaksi->barang->nama_barang }}</td>
            </tr>
            <tr>
                <td>Harga</td>
                <td class="right">Rp {{ number_format($transaksi->harga_pokok, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Qty</td>
                <td class="right">{{ $transaksi->jumlah_penjualan }}</td>
            </tr>
            <tr>
                <td>Subtotal</td>
                <td class="right">Rp {{ number_format($transaksi->subtotal, 0, ',', '.') }}</td>
            </tr>
        </table>
        <hr />
        <div class="total-row flex justify-between font-bold">
            <span>Total Harga:</span>
            <span class="right">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
        </div>
    </div>
</body>

</html>
