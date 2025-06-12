{{-- resources/views/nota-pdf.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: monospace;
            font-size: 12px;
            margin: 0;
            padding: 5px;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        hr {
            border: dashed 1px #333;
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0;
        }

        td {
            padding: 3px 0;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="center" style="font-weight:bold;">GRYA PARFUM</div>
    <div class="center">Jln.pasir putih kedonganan kec kuta kab badung</div>
    <div class="center small">Telp. 081234567</div>
    <hr />
    <div>No Nota: PNJ-{{ $transaksi->id }}</div>
    <div>Tanggal: {{ $transaksi->tanggal_transaksi->format('d-m-Y H:i') }}</div>
    <div>Kasir: {{ $transaksi->kasir->username ?? '-' }}</div>
    <hr />
    <table>
        <tr>
            <td>Barang</td>
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
        <tr>
            <td>Pajak ({{ $transaksi->pajak->presentase }}%)</td>
            <td class="right">
                Rp {{ number_format($transaksi->total_harga - $transaksi->subtotal, 0, ',', '.') }}
            </td>
        </tr>
    </table>
    <hr />
    <div class="total-row">
        <span>Total Harga:</span>
        <span class="right">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
    </div>
</body>

</html>
