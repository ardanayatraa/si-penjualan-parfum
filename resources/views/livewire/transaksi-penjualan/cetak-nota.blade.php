<div>
    <x-dialog-modal wire:model="open">
        <x-slot name="title">
            Nota Penjualan #PNJ-{{ $transaksi->id }}
        </x-slot>

        <x-slot name="content">
            <div class="space-y-2 font-mono text-sm">
                <div class="text-center font-bold">SI PARFUM</div>
                <div class="text-center">No Nota: PNJ-{{ $transaksi->id }}</div>
                <div class="text-center small">
                    {{ $transaksi->tanggal_transaksi->format('d-m-Y H:i') }}
                </div>
                <hr class="border-dashed my-2" />

                <table class="w-full text-sm">
                    <tr>
                        <td>Barang</td>
                        <td class="text-right">{{ $transaksi->barang->nama_barang }}</td>
                    </tr>
                    <tr>
                        <td>Harga</td>
                        <td class="text-right">
                            Rp {{ number_format($transaksi->harga_pokok, 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr>
                        <td>Qty</td>
                        <td class="text-right">{{ $transaksi->jumlah_penjualan }}</td>
                    </tr>
                    <tr>
                        <td>Subtotal</td>
                        <td class="text-right">
                            Rp {{ number_format($transaksi->subtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                </table>

                <hr class="border-dashed my-2" />

                <div class="flex justify-between font-bold">
                    <span>Total Harga:</span>
                    <span>
                        Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-end gap-2">
                <x-button wire:click="print" class="bg-blue-600 hover:bg-blue-700">
                    Cetak PDF
                </x-button>
                <x-button wire:click="$set('open', false)" class="bg-gray-500 hover:bg-gray-600">
                    Tutup
                </x-button>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>
