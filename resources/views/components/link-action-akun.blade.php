<div>
    @unless (request()->routeIs('barang'))
        <x-button wire:click="delete({{ $id }})">Hapus</x-button>
    @endunless
</div>
