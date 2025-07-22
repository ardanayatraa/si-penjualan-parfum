<div>
    <x-dialog-modal wire:model="open">
        <x-slot name="title">Edit Hutang</x-slot>

        <x-slot name="content">
            <div class="space-y-4">
                {{-- Pilih Pembelian --}}
                <div>
                    <x-label value="Pembelian" />
                    <select wire:model.defer="id_pembelian" class="w-full border-gray-300 rounded-md">
                        <option value="">-- Pilih Pembelian --</option>
                        @foreach ($listPembelian as $pb)
                            <option value="{{ $pb->id }}">
                                #{{ $pb->id }} - {{ $pb->tanggal_transaksi }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_pembelian')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Pilih Supplier --}}
                <div>
                    <x-label value="Supplier" />
                    <select wire:model.defer="id_supplier" class="w-full border-gray-300 rounded-md">
                        <option value="">-- Pilih Supplier --</option>
                        @foreach ($listSupplier as $sp)
                            <option value="{{ $sp->id_supplier }}">{{ $sp->nama_supplier }}</option>
                        @endforeach
                    </select>
                    @error('id_supplier')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Jumlah --}}
                <div>
                    <x-label value="Jumlah" />
                    <x-input type="number" wire:model.defer="jumlah" class="w-full" />
                    @error('jumlah')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                {{-- Jumlah Dibayarkan --}}
                <div>
                    <x-label value="Jumlah Dibayarkan" />
                    <x-input type="number" wire:model.defer="jumlah_dibayarkan" class="w-full" />
                    @error('jumlah_dibayarkan')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Tanggal Tempo --}}
                <div>
                    <x-label value="Tanggal Jatuh Tempo" />
                    <x-input type="date" wire:model.defer="tgl_tempo" class="w-full" />
                    @error('tgl_tempo')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Status --}}
                <div>
                    <x-label value="Status" />
                    <x-input wire:model.defer="status" class="w-full" />
                    @error('status')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-button wire:click="update" class="bg-blue-600 hover:bg-blue-700">Update</x-button>
            <x-button wire:click="$set('open', false)" class="bg-gray-500 hover:bg-gray-600">Batal</x-button>
        </x-slot>
    </x-dialog-modal>
</div>
