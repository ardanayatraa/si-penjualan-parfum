<div class="flex gap-2">
    <x-button wire:click="edit({{ $id }})">Edit</x-button>
    <x-button wire:click="createReturn({{ $id }})" class="bg-yellow-500 hover:bg-yellow-600">Return
        Barang</x-button>
</div>
