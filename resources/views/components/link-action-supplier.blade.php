<div>
    @unless (request()->routeIs('barang'))
        <x-button wire:click="delete({{ $id }})">Hapus</x-button>
    @endunless
    <x-button wire:click="edit({{ $id }})">Edit</x-button>

    <x-button wire:click="show({{ $id }})">Lihat Barang</x-button>


</div>
