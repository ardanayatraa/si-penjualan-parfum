<x-app-layout>
    <div class="p-4 sm:p-6 space-y-6">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 sm:p-6">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white">Manajemen Transaksi Pembelian</h1>
        </div>

        <!-- Form + Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 sm:p-6 space-y-6">
            @livewire('transaksi-pembelian.create')

            <!-- Table Wrapper: biar scrollable di HP -->
            <div class="overflow-x-auto">
                @livewire('table.transaksi-pembelian-table')
            </div>
        </div>
    </div>

    <!-- Modal Update & Delete -->
    @livewire('transaksi-pembelian.update')
    @livewire('transaksi-pembelian.delete')
</x-app-layout>
