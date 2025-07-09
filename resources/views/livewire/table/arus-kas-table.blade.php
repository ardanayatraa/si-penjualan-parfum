<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
        <h2 class="text-lg font-bold mb-2 text-green-700">Pemasukan</h2>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-2">Keterangan</th>
                    <th class="px-4 py-2">Jumlah</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800">
                @foreach ($dataPemasukan as $item)
                    <tr>
                        <td class="px-4 py-2">{{ $item['keterangan'] }}</td>
                        <td class="px-4 py-2">{{ number_format($item['jumlah'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="font-bold">
                    <td class="px-4 py-2">Total Pemasukan</td>
                    <td class="px-4 py-2 text-green-700">{{ number_format($pemasukan, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
        <h2 class="text-lg font-bold mb-2 text-red-700">Pengeluaran</h2>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-2">Keterangan</th>
                    <th class="px-4 py-2">Jumlah</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800">
                @foreach ($dataPengeluaran as $item)
                    <tr>
                        <td class="px-4 py-2">{{ $item['keterangan'] }}</td>
                        <td class="px-4 py-2">{{ number_format($item['jumlah'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="font-bold">
                    <td class="px-4 py-2">Total Pengeluaran</td>
                    <td class="px-4 py-2 text-red-700">{{ number_format($pengeluaran, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div> 