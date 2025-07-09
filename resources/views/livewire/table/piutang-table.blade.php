<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th class="px-4 py-2">Penjualan</th>
                <th class="px-4 py-2">Jumlah</th>
                <th class="px-4 py-2">Status</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800">
            @foreach ($piutangs as $p)
                <tr>
                    <td class="px-4 py-2">{{ $p->penjualan->id_penjualan ?? '-' }}</td>
                    <td class="px-4 py-2">{{ number_format($p->jumlah, 0, ',', '.') }}</td>
                    <td class="px-4 py-2">{{ $p->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div> 