<div>
    <x-button wire:click="$set('open', true)" class="bg-green-600 mb-2 hover:bg-green-700">
        Tambah Jurnal Umum
    </x-button>

    <x-dialog-modal wire:model="open" max-width="2xl">
        <x-slot name="title">Tambah Jurnal Umum</x-slot>
        <x-slot name="content">
            <div class="space-y-4">
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <x-label for="tanggal" value="Tanggal" />
                        <x-input type="date" id="tanggal" wire:model.defer="tanggal" />
                        @error('tanggal')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <x-label for="no_bukti" value="No. Bukti" />
                        <x-input id="no_bukti" wire:model.defer="no_bukti" />
                        @error('no_bukti')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <x-label for="keterangan" value="Keterangan" />
                        <x-input id="keterangan" wire:model.defer="keterangan" />
                        @error('keterangan')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <h3 class="font-semibold">Detail Jurnal</h3>
                <table class="w-full table-auto border-collapse">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border px-2 py-1">Akun</th>
                            <th class="border px-2 py-1">Debit</th>
                            <th class="border px-2 py-1">Kredit</th>
                            <th class="border px-2 py-1">#</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($details as $i => $row)
                            <tr>
                                <td class="border px-2 py-1">
                                    <select wire:model.defer="details.{{ $i }}.akun_id"
                                        class="w-full border-gray-300 rounded">
                                        <option value="">-- Pilih Akun --</option>
                                        @foreach ($listAkun as $a)
                                            <option value="{{ $a->id }}">{{ $a->kode_akun }} -
                                                {{ $a->nama_akun }}</option>
                                        @endforeach
                                    </select>
                                    @error("details.$i.akun_id")
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </td>
                                <td class="border px-2 py-1">
                                    <x-input type="number" wire:model.defer="details.{{ $i }}.debit" />
                                    @error("details.$i.debit")
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </td>
                                <td class="border px-2 py-1">
                                    <x-input type="number" wire:model.defer="details.{{ $i }}.kredit" />
                                    @error("details.$i.kredit")
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </td>
                                <td class="border px-2 py-1 text-center">
                                    <x-button wire:click.prevent="removeDetail({{ $i }})"
                                        class="bg-red-500 hover:bg-red-600 text-xs">
                                        &times;
                                    </x-button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <x-button wire:click.prevent="addDetail" class="mt-2 text-sm">+ Tambah Baris</x-button>
            </div>
        </x-slot>
        <x-slot name="footer">
            <div class="flex justify-end gap-2">
                <x-button wire:click="store" class="bg-blue-600 hover:bg-blue-700">Simpan</x-button>
                <x-button wire:click="$set('open', false)" class="bg-gray-500 hover:bg-gray-600">Batal</x-button>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>
