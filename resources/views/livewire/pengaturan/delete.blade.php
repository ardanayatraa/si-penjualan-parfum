<div>
    <x-dialog-modal wire:model="open">
        <x-slot name="title">Hapus Pengaturan</x-slot>
        <x-slot name="content">
            <p>Anda yakin ingin menghapus pengaturan <strong>{{ $pk }}</strong>?</p>
        </x-slot>
        <x-slot name="footer">
            <div class="flex justify-end gap-2">
                <x-button wire:click="destroy" class="bg-red-600 hover:bg-red-700">Hapus</x-button>
                <x-button wire:click="$set('open', false)" class="bg-gray-500 hover:bg-gray-600">Batal</x-button>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>
