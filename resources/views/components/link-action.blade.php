<div>
    @unless (request()->routeIs('barang'))
        <x-button wire:click="delete({{ $id }})">Hapus</x-button>
    @endunless
    <x-button wire:click="edit({{ $id }})">Edit</x-button>
</div>
