<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th class="px-4 py-2">Supplier</th>
                <th class="px-4 py-2">Pembelian</th>
                <th class="px-4 py-2">Jumlah</th>
                <th class="px-4 py-2">Tgl Tempo</th>
                <th class="px-4 py-2">Status</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800">
            @foreach ($hutangs as $h)
                <tr>
                    <td class="px-4 py-2">{{ $h->supplier->nama_supplier ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $h->pembelian->id_pembelian ?? '-' }}</td>
                    <td class="px-4 py-2">{{ number_format($h->jumlah, 0, ',', '.') }}</td>
                    <td class="px-4 py-2">{{ $h->tgl_tempo }}</td>
                    <td class="px-4 py-2">{{ $h->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div> 