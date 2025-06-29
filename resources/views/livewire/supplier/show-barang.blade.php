<div>
    <x-dialog-modal wire:model="open">
        <x-slot name="title">
            Supplier: {{ $supplierName }}
        </x-slot>

        <x-slot name="content">
            <div class="space-y-4">
                <div>
                    <p class="font-medium">Daftar Barang:</p>
                    @if ($barangs->isEmpty())
                        <p class="text-sm text-gray-600 mt-2">Belum ada barang terkait.</p>
                    @else
                        <ul class="list-disc list-inside mt-2 space-y-1">
                            @foreach ($barangs as $b)
                                <li>
                                    {{ $b->nama_barang }}
                                    (Stok: {{ $b->stok }},
                                    Harga beli: Rp{{ number_format($b->harga_beli, 0, ',', '.') }})
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-button wire:click="$set('open', false)" class="bg-gray-500 hover:bg-gray-600 text-white">
                Tutup
            </x-button>
        </x-slot>
    </x-dialog-modal>
</div>
