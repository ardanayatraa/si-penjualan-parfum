<!-- resources/views/accounting.blade.php -->
<x-app-layout>
    <div class="p-4 sm:p-6 space-y-6" x-data="{ tab: 'akun' }" x-cloak>
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 sm:p-6">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white">
                Manajemen Data Accounting
            </h1>
        </div>

        <!-- Tabs Navigation -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 sm:p-6">
            <nav class="flex space-x-4 border-b border-gray-200 dark:border-gray-700">
                <button @click="tab = 'akun'"
                    :class="tab === 'akun'
                        ?
                        'text-indigo-600 border-b-2 border-indigo-600' :
                        'text-gray-600 hover:text-gray-800 dark:text-gray-300 dark:hover:text-white'"
                    class="pb-2 font-medium">
                    Akun
                </button>
                <button @click="tab = 'jurnal'"
                    :class="tab === 'jurnal'
                        ?
                        'text-indigo-600 border-b-2 border-indigo-600' :
                        'text-gray-600 hover:text-gray-800 dark:text-gray-300 dark:hover:text-white'"
                    class="pb-2 font-medium">
                    Jurnal Umum
                </button>
                <button @click="tab = 'pengeluaran'"
                    :class="tab === 'pengeluaran'
                        ?
                        'text-indigo-600 border-b-2 border-indigo-600' :
                        'text-gray-600 hover:text-gray-800 dark:text-gray-300 dark:hover:text-white'"
                    class="pb-2 font-medium">
                    Pengeluaran
                </button>
                <button @click="tab = 'hutang'"
                    :class="tab === 'hutang'
                        ?
                        'text-indigo-600 border-b-2 border-indigo-600' :
                        'text-gray-600 hover:text-gray-800 dark:text-gray-300 dark:hover:text-white'"
                    class="pb-2 font-medium">
                    Hutang
                </button>
                <button @click="tab = 'piutang'"
                    :class="tab === 'piutang'
                        ?
                        'text-indigo-600 border-b-2 border-indigo-600' :
                        'text-gray-600 hover:text-gray-800 dark:text-gray-300 dark:hover:text-white'"
                    class="pb-2 font-medium">
                    Piutang
                </button>
            </nav>

            <div class="mt-6 space-y-6">
                <!-- Akun Tab -->
                <section x-show="tab === 'akun'">
                    @livewire('akun.create')
                    <div class="overflow-x-auto">
                        @livewire('table.akun-table')
                    </div>
                </section>

                <!-- Jurnal Umum Tab -->
                <section x-show="tab === 'jurnal'">
                    @livewire('jurnal-umum.create')
                    <div class="overflow-x-auto">
                        @livewire('table.jurnal-umum-table')
                    </div>
                </section>

                <!-- Pengeluaran Tab -->
                <section x-show="tab === 'pengeluaran'">
                    @livewire('pengeluaran.create')
                    <div class="overflow-x-auto">
                        @livewire('table.pengeluaran-table')
                    </div>
                </section>

                <!-- Hutang Tab -->
                <section x-show="tab === 'hutang'">
                    @include('livewire.hutang.index')
                </section>

                <!-- Piutang Tab -->
                <section x-show="tab === 'piutang'">
                    @livewire('piutang.create')
                    <div class="overflow-x-auto">
                        @livewire('table.piutang-table')
                    </div>
                </section>
            </div>
        </div>
    </div>

    <!-- Modals Update & Delete Akun -->
    @livewire('akun.update')
    @livewire('akun.delete')

    <!-- Modals Update & Delete Pengeluaran -->
    @livewire('pengeluaran.update')
    @livewire('pengeluaran.delete')

    <!-- Modals Update & Delete Hutang -->
    @livewire('hutang.update')
    @livewire('hutang.delete')

    <!-- Modals Update & Delete Piutang -->
    @livewire('piutang.update')
    @livewire('piutang.delete')




    <!-- Modals Update & Delete Jurnal Umum -->
    @livewire('jurnal-umum.update')
    @livewire('jurnal-umum.delete')
</x-app-layout>
