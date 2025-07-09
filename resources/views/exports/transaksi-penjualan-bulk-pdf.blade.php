{{-- resources/views/exports/transaksi-penjualan-bulk-pdf.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi Penjualan</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 20px;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0 0 5px;
            font-size: 18px;
        }

        .header .period {
            margin: 5px 0;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: center;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-selesai {
            background-color: #d4edda;
            color: #155724;
        }

        .status-dibatalkan {
            background-color: #f8d7da;
            color: #721c24;
        }

        .summary {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
        }

        .summary h3 {
            margin: 0 0 10px;
            font-size: 14px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .footer {
            position: fixed;
            bottom: 20px;
            width: 100%;
            text-align: center;
            font-size: 8px;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>LAPORAN TRANSAKSI PENJUALAN</h1>
        <div class="period">
            Diekspor: {{ now()->format('d M Y H:i') }} |
            Total: {{ $transaksis->count() }} transaksi
        </div>

        <div class="footer">
            <p>Laporan ini digenerate otomatis oleh sistem pada {{ now()->format('d M Y H:i:s') }}</p>
        </div>
</body>

</html>>
</div>

<table>
    <thead>
        <tr>
            <th style="width: 5%;">No</th>
            <th style="width: 8%;">Kode</th>
            <th style="width: 12%;">Tanggal</th>
            <th style="width: 10%;">Kasir</th>
            <th style="width: 15%;">Barang</th>
            <th style="width: 5%;">Qty</th>
            <th style="width: 10%;">Subtotal</th>
            <th style="width: 8%;">Pajak</th>
            <th style="width: 10%;">Total</th>
            <th style="width: 8%;">Laba</th>
            <th style="width: 9%;">Status</th>
        </tr>
    </thead>
    <tbody>
        @php
            $totalSubtotal = 0;
            $totalPajak = 0;
            $totalHarga = 0;
            $totalLaba = 0;
            $countSelesai = 0;
            $countPending = 0;
            $countDibatalkan = 0;
        @endphp

        @foreach ($transaksis as $index => $transaksi)
            @php
                $pajakAmount = 0;
                if ($transaksi->pajak) {
                    $pajakAmount = ($transaksi->subtotal * $transaksi->pajak->presentase) / 100;
                }

                if ($transaksi->status === 'selesai') {
                    $totalSubtotal += $transaksi->subtotal;
                    $totalPajak += $pajakAmount;
                    $totalHarga += $transaksi->total_harga;
                    $totalLaba += $transaksi->laba_bruto;
                    $countSelesai++;
                } elseif ($transaksi->status === 'pending') {
                    $countPending++;
                } else {
                    $countDibatalkan++;
                }
            @endphp

            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $transaksi->kode_transaksi }}</td>
                <td class="text-center">{{ $transaksi->tanggal_transaksi->format('d-m-Y H:i') }}</td>
                <td>{{ $transaksi->kasir->username ?? '-' }}</td>
                <td>{{ $transaksi->barang->nama_barang ?? '-' }}</td>
                <td class="text-center">{{ $transaksi->jumlah_terjual }}</td>
                <td class="text-right">Rp {{ number_format($transaksi->subtotal, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($pajakAmount, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($transaksi->laba_bruto, 0, ',', '.') }}</td>
                <td class="text-center">
                    <span class="status-badge status-{{ $transaksi->status }}">
                        {{ strtoupper($transaksi->status) }}
                    </span>
                </td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background-color: #e9ecef; font-weight: bold;">
            <td colspan="6" class="text-center">TOTAL (Transaksi Selesai)</td>
            <td class="text-right">Rp {{ number_format($totalSubtotal, 0, ',', '.') }}</td>
            <td class="text-right">Rp {{ number_format($totalPajak, 0, ',', '.') }}</td>
            <td class="text-right">Rp {{ number_format($totalHarga, 0, ',', '.') }}</td>
            <td class="text-right">Rp {{ number_format($totalLaba, 0, ',', '.') }}</td>
            <td></td>
        </tr>
    </tfoot>
</table>

<div class="summary">
    <h3>Ringkasan Laporan</h3>
    <div class="summary-item">
        <span>Total Transaksi:</span>
        <span>{{ $transaksis->count() }} transaksi</span>
    </div>
    <div class="summary-item">
        <span>Transaksi Selesai:</span>
        <span>{{ $countSelesai }} transaksi</span>
    </div>
    <div class="summary-item">
        <span>Transaksi Pending:</span>
        <span>{{ $countPending }} transaksi</span>
    </div>
    <div class="summary-item">
        <span>Transaksi Dibatalkan:</span>
        <span>{{ $countDibatalkan }} transaksi</span>
    </div>
    <div class="summary-item">
        <span>Total Penjualan (Selesai):</span>
        <span>Rp {{ number_format($totalHarga, 0, ',', '.') }}</span>
    </div>
    <div class="summary-item">
        <span>Total Laba Bruto:</span>
        <span>Rp {{ number_format($totalLaba, 0, ',', '.') }}</span>
    </div>
    @if ($countSelesai > 0)
        <div class="summary-item">
            <span>Rata-rata per Transaksi:</span>
            <span>Rp {{ number_format($totalHarga / $countSelesai, 0, ',', '.') }}</span>
        </div>
        <div class="summary-item">
            <span>Margin Laba:</span>
            <span>{{ $totalSubtotal > 0 ? number_format(($totalLaba / $totalSubtotal) * 100, 2) : '0' }}%</span>
        </div>
    @endif
</div
