{{-- resources/views/exports/nota-pdf.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'Courier New', monospace;
            font-size: 11px;
            margin: 0;
            padding: 8px;
            line-height: 1.3;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .left {
            text-align: left;
        }

        hr {
            border: none;
            border-top: 1px dashed #333;
            margin: 8px 0;
        }

        .header {
            margin-bottom: 10px;
        }

        .store-name {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 2px;
        }

        .store-info {
            font-size: 10px;
            margin-bottom: 1px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0;
        }

        td {
            padding: 2px 0;
            vertical-align: top;
        }

        .item-row td {
            padding: 1px 0;
        }

        .total-section {
            margin-top: 5px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 2px 0;
        }

        .grand-total {
            font-weight: bold;
            font-size: 12px;
            border-top: 1px solid #333;
            padding-top: 3px;
            margin-top: 3px;
        }

        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 10px;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 9px;
            font-weight: bold;
            border-radius: 3px;
            margin-left: 5px;
        }

        .status-selesai {
            background-color: #d4edda;
            color: #155724;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
    </style>
</head>

<body>
    {{-- Header Toko --}}
    <div class="header center">
        <div class="store-name">GRYA PARFUM</div>
        <div class="store-info">Jln. Pasir Putih Kedonganan</div>
        <div class="store-info">Kec. Kuta Kab. Badung</div>
        <div class="store-info">Telp. 081234567</div>
    </div>

    <hr />

    {{-- Info Transaksi --}}
    <table>
        <tr>
            <td style="width: 40%;">No Nota</td>
            <td style="width: 5%;">:</td>
            <td>{{ $transaksi->kode_transaksi }}</td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>:</td>
            <td>{{ $transaksi->tanggal_transaksi->format('d-m-Y H:i') }}</td>
        </tr>
        <tr>
            <td>Kasir</td>
            <td>:</td>
            <td>{{ $transaksi->kasir->username ?? 'Unknown' }}</td>
        </tr>
        <tr>
            <td>Pembayaran</td>
            <td>:</td>
            <td>
                @php
                    $metodePembayaran = [
                        'cash' => 'Tunai',
                        'transfer' => 'Transfer Bank',
                        'debit_card' => 'Kartu Debit',
                        'credit_card' => 'Kartu Kredit',
                        'e_wallet' => 'E-Wallet',
                    ];
                @endphp
                {{ $metodePembayaran[$transaksi->metode_pembayaran] ?? $transaksi->metode_pembayaran }}
            </td>
        </tr>
        <tr>
            <td>Status</td>
            <td>:</td>
            <td>
                <span class="status-badge status-{{ $transaksi->status }}">
                    {{ strtoupper($transaksi->status) }}
                </span>
            </td>
        </tr>
    </table>

    <hr />

    {{-- Detail Barang --}}
    <table>
        <tr class="item-row">
            <td colspan="3" style="font-weight: bold;">{{ $transaksi->barang->nama_barang }}</td>
        </tr>
        <tr class="item-row">
            <td style="width: 50%;">{{ $transaksi->jumlah_terjual }} x Rp
                {{ number_format($transaksi->barang->harga_jual, 0, ',', '.') }}</td>
            <td style="width: 10%;"></td>
            <td class="right">Rp {{ number_format($transaksi->subtotal, 0, ',', '.') }}</td>
        </tr>
    </table>

    <hr />

    {{-- Ringkasan Harga --}}
    <div class="total-section">
        <div class="total-row">
            <span>Subtotal:</span>
            <span>Rp {{ number_format($transaksi->subtotal, 0, ',', '.') }}</span>
        </div>

        @if ($transaksi->pajak && $pajakAmount > 0)
            <div class="total-row">
                <span>Pajak ({{ $transaksi->pajak->presentase }}%):</span>
                <span>Rp {{ number_format($pajakAmount, 0, ',', '.') }}</span>
            </div>
        @endif

        <div class="total-row grand-total">
            <span>TOTAL BAYAR:</span>
            <span>Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
        </div>

        @if ($transaksi->laba_bruto > 0)
            <div class="total-row" style="font-size: 10px; color: #666;">
                <span>Laba Bruto:</span>
                <span>Rp {{ number_format($transaksi->laba_bruto, 0, ',', '.') }}</span>
            </div>
        @endif
    </div>

    <hr />

    {{-- Footer --}}
    <div class="footer">
        <div>Terima kasih atas kunjungan Anda!</div>
        <div style="margin-top: 5px;">Barang yang sudah dibeli</div>
        <div>tidak dapat dikembalikan</div>
        <div style="margin-top: 8px; font-size: 9px;">
            Dicetak: {{ now()->format('d-m-Y H:i:s') }}
        </div>

        {{-- QR Code atau info tambahan bisa ditambahkan di sini --}}
        <div style="margin-top: 10px; font-size: 9px;">
            Follow us: @gryaparfum
        </div>
    </div>
</body>

</html>
