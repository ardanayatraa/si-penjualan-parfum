<x-app-layout>
    <div class="p-4 sm:p-6 space-y-6">

        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 sm:p-6">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white">Laporan</h1>
        </div>

        <!-- Tabs -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
            <div class="flex border-b border-gray-200 dark:border-gray-700">
                <button onclick="showTab('penjualan')"
                    class="tab-btn px-4 py-2 text-sm font-medium text-gray-700 dark:text-white border-b-2"
                    id="tab-penjualan">
                    Laporan Penjualan
                </button>
                <button onclick="showTab('pembelian')"
                    class="tab-btn px-4 py-2 text-sm font-medium text-gray-700 dark:text-white border-b-2"
                    id="tab-pembelian">
                    Laporan Pembelian
                </button>
                <button onclick="showTab('stok')"
                    class="tab-btn px-4 py-2 text-sm font-medium text-gray-700 dark:text-white border-b-2"
                    id="tab-stok">
                    Laporan Stok
                </button>
                <button onclick="showTab('labarugi')"
                    class="tab-btn px-4 py-2 text-sm font-medium text-gray-700 dark:text-white border-b-2"
                    id="tab-labarugi">
                    Laporan Laba Rugi
                </button>
                <button onclick="showTab('neraca')"
                    class="tab-btn px-4 py-2 text-sm font-medium text-gray-700 dark:text-white border-b-2"
                    id="tab-neraca">
                    Laporan Neraca
                </button>
                <button onclick="showTab('aruskas')"
                    class="tab-btn px-4 py-2 text-sm font-medium text-gray-700 dark:text-white border-b-2"
                    id="tab-aruskas">
                    Laporan Arus Kas
                </button>
            </div>

            <div class="p-4 sm:p-6 space-y-6">
                <!-- Penjualan -->
                <div id="content-penjualan">
                    @livewire('table.laporan-penjualan-table')
                </div>

                <!-- Pembelian -->
                <div id="content-pembelian" class="hidden">
                    {{-- @livewire('table.laporan-pembelian-table') --}}
                </div>

                <!-- Stok -->
                <div id="content-stok" class="hidden">
                    {{-- @livewire('table.laporan-stok-table') --}}
                </div>

                <!-- Laba Rugi -->
                <div id="content-labarugi" class="hidden">
                    {{-- @livewire('table.laporan-laba-rugi-table') --}}
                </div>

                <!-- Neraca -->
                <div id="content-neraca" class="hidden">
                    {{-- @livewire('table.laporan-neraca-table') --}}
                </div>

                <!-- Arus Kas -->
                <div id="content-aruskas" class="hidden">
                    {{-- @livewire('table.arus-kas-table') --}}
                </div>
            </div>
        </div>
    </div>

    <!-- Simple Tab Script -->
    <script>
        function showTab(tab) {
            const tabs = ['penjualan', 'pembelian', 'stok', 'labarugi', 'neraca', 'aruskas'];

            tabs.forEach(t => {
                document.getElementById('content-' + t).classList.add('hidden');
                document.getElementById('tab-' + t).classList.remove('border-blue-600', 'text-blue-600',
                    'font-semibold');
            });

            document.getElementById('content-' + tab).classList.remove('hidden');
            document.getElementById('tab-' + tab).classList.add('border-blue-600', 'text-blue-600', 'font-semibold');
        }

        // Default tab
        document.addEventListener('DOMContentLoaded', function() {
            showTab('penjualan');
        });
    </script>
</x-app-layout>
