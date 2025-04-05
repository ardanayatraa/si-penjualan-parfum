<x-app-layout>
    <div class="p-4 sm:p-6 space-y-6">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 sm:p-6">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white">Manajemen Pajak Transaksi</h1>
        </div>

        <!-- Form + Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 sm:p-6 space-y-6">
            @livewire('pajak-transaksi.create')

            <!-- Table Wrapper: biar scrollable di HP -->
            <div class="overflow-x-auto">
                @livewire('table.pajak-transaksi-table')
            </div>
        </div>
    </div>

    <!-- Modal Update & Delete -->
    @livewire('pajak-transaksi.update')
    @livewire('pajak-transaksi.delete')
</x-app-layout>
