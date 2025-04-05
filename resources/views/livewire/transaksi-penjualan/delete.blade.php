<div>
    <x-dialog-modal wire:model="open">
        <x-slot name="title">
            Hapus Transaksi Penjualan
        </x-slot>

        <x-slot name="content">
            <p class="text-sm text-gray-600">
                Yakin ingin menghapus transaksi penjualan ini? Data akan hilang secara permanen.
            </p>
        </x-slot>

        <x-slot name="footer">
            <x-button wire:click="delete" class="bg-red-600 hover:bg-red-700">
                Hapus
            </x-button>

            <x-button wire:click="$set('open', false)" class="bg-gray-500 hover:bg-gray-600">
                Batal
            </x-button>
        </x-slot>
    </x-dialog-modal>
</div>
