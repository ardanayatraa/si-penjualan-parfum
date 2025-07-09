<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th class="px-4 py-2">Tanggal</th>
                <th class="px-4 py-2">Akun Beban</th>
                <th class="px-4 py-2">Pencatat</th>
                <th class="px-4 py-2">Jenis</th>
                <th class="px-4 py-2">Jumlah</th>
                <th class="px-4 py-2">Keterangan</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800">
            @foreach ($pengeluarans as $p)
                <tr>
                    <td class="px-4 py-2">{{ $p->tanggal }}</td>
                    <td class="px-4 py-2">{{ $p->akun->nama_akun ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $p->user->username ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $p->jenis_pengeluaran }}</td>
                    <td class="px-4 py-2">{{ number_format($p->jumlah, 0, ',', '.') }}</td>
                    <td class="px-4 py-2">{{ $p->keterangan }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div> 