<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />

    {{-- <script src="https://cdn.tailwindcss.com"></script> --}}

    <!-- Adds the Core Table Styles -->
    @rappasoftTableStyles

    <!-- Adds any relevant Third-Party Styles (Used for DateRangeFilter (Flatpickr) and NumberRangeFilter) -->
    @rappasoftTableThirdPartyStyles
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles
    <style>
        @media (max-width: 767px) {
            #sidebar {
                background-color: rgb(255, 237, 213) !important;
                /* Solid orange-100 color */
            }
        }

        /* Active menu item styles */
        .sidebar-active {
            background-color: #FFEDD5 !important;
            /* orange-200 */
            border-left: 4px solid #F97316 !important;
            /* orange-500 */
            font-weight: 600 !important;
            color: #C2410C !important;
            /* orange-700 */
        }

        /* Adjust padding for active items to account for border */
        .sidebar-active {
            padding-left: 10px !important;
        }
    </style>
    @stack('head')
</head>

<body class="bg-gray-50">
    <div>
        <!-- Sidebar -->
        <aside id="sidebar"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-orange-100 md:bg-orange-100 border-r border-orange-200 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out shadow-lg">
            <div class="flex flex-col h-full">
                <!-- Logo -->
                <div class="flex items-center justify-between h-16 px-4 border-b border-orange-200 bg-orange-50">
                    <div class="flex items-center space-x-2">
                        <svg class="w-8 h-8 text-orange-500" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L2 7L12 12L22 7L12 2Z" fill="currentColor" />
                            <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                        <span class="text-lg font-bold text-orange-700">GRIYA PARFUM</span>
                    </div>
                    <button id="closeSidebarBtn" class="p-1 rounded-md md:hidden hover:bg-orange-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-3 py-4 overflow-y-auto text-orange-800">
                    <div class="space-y-2">
                        <a href="{{ route('dashboard') }}"
                            class="flex items-center px-3 py-2 text-sm font-medium rounded-lg hover:bg-orange-200 transition-all {{ request()->routeIs('dashboard') ? 'sidebar-active' : '' }}">
                            <i class="fas fa-tachometer-alt w-5 h-5 mr-3"></i>
                            <span>Dashboard</span>
                        </a>
                        {{-- Menu untuk ADMIN --}}
                        @if (auth()->user()->level === 'admin')
                            <a href="{{ route('barang') }}"
                                class="flex items-center px-3 py-2 text-sm font-medium rounded-lg hover:bg-orange-200 transition-all {{ request()->routeIs('barang') ? 'sidebar-active' : '' }}">
                                <i class="fas fa-box w-5 h-5 mr-3"></i>
                                <span>Barang</span>
                            </a>

                            <a href="{{ route('supplier') }}"
                                class="flex items-center px-3 py-2 text-sm font-medium rounded-lg hover:bg-orange-200 transition-all {{ request()->routeIs('supplier') ? 'sidebar-active' : '' }}">
                                <i class="fas fa-truck w-5 h-5 mr-3"></i>
                                <span>Supplier</span>
                            </a>

                            <a href="{{ route('transaksi-pembelian') }}"
                                class="flex items-center px-3 py-2 text-sm font-medium rounded-lg hover:bg-orange-200 transition-all {{ request()->routeIs('transaksi-pembelian') ? 'sidebar-active' : '' }}">
                                <i class="fas fa-shopping-cart w-5 h-5 mr-3"></i>
                                <span>Transaksi Pembelian</span>
                            </a>

                            <a href="{{ route('transaksi-penjualan') }}"
                                class="flex items-center px-3 py-2 text-sm font-medium rounded-lg hover:bg-orange-200 transition-all {{ request()->routeIs('transaksi-penjualan') ? 'sidebar-active' : '' }}">
                                <i class="fas fa-cash-register w-5 h-5 mr-3"></i>
                                <span>Transaksi Penjualan</span>
                            </a>

                            <a href="{{ route('pajak-transaksi') }}"
                                class="flex items-center px-3 py-2 text-sm font-medium rounded-lg hover:bg-orange-200 transition-all {{ request()->routeIs('pajak-transaksi') ? 'sidebar-active' : '' }}">
                                <i class="fas fa-receipt w-5 h-5 mr-3"></i>
                                <span>Pajak Transaksi</span>
                            </a>

                            <a href="{{ route('return-barang') }}"
                                class="flex items-center px-3 py-2 text-sm font-medium rounded-lg hover:bg-orange-200 transition-all {{ request()->routeIs('return-barang') ? 'sidebar-active' : '' }}">
                                <i class="fas fa-undo w-5 h-5 mr-3"></i>
                                <span>Return Barang</span>
                            </a>


                            <a href="{{ route('user') }}"
                                class="flex items-center px-3 py-2 text-sm font-medium rounded-lg hover:bg-orange-200 transition-all {{ request()->routeIs('user') ? 'sidebar-active' : '' }}">
                                <i class="fas fa-users w-5 h-5 mr-3"></i>
                                <span>User</span>
                            </a>



                            <a href="{{ route('laporan') }}"
                                class="flex items-center px-3 py-2 text-sm font-medium rounded-lg hover:bg-orange-200 transition-all {{ request()->routeIs('laporan') ? 'sidebar-active' : '' }}">
                                <i class="fas fa-chart-bar w-5 h-5 mr-3"></i>
                                <span>Laporan</span>
                            </a>

                            <a href="{{ route('grafik.penjualan') }}"
                                class="flex items-center px-3 py-2 text-sm font-medium rounded-lg hover:bg-orange-200 transition-all {{ request()->routeIs('grafik.penjualan') ? 'sidebar-active' : '' }}">
                                <i class="fas fa-chart-line w-5 h-5 mr-3"></i>
                                <span>Grafik Penjualan</span>
                            </a>
                        @endif

                        {{-- Menu untuk KASIR --}}
                        @if (auth()->user()->level === 'kasir')
                            <a href="{{ route('transaksi-pembelian') }}"
                                class="flex items-center px-3 py-2 text-sm font-medium rounded-lg hover:bg-orange-200 transition-all {{ request()->routeIs('transaksi-pembelian') ? 'sidebar-active' : '' }}">
                                <i class="fas fa-shopping-cart w-5 h-5 mr-3"></i>
                                <span>Transaksi Pembelian</span>
                            </a>

                            <a href="{{ route('transaksi-penjualan') }}"
                                class="flex items-center px-3 py-2 text-sm font-medium rounded-lg hover:bg-orange-200 transition-all {{ request()->routeIs('transaksi-penjualan') ? 'sidebar-active' : '' }}">
                                <i class="fas fa-cash-register w-5 h-5 mr-3"></i>
                                <span>Transaksi Penjualan</span>
                            </a>

                            <a href="{{ route('laporan') }}"
                                class="flex items-center px-3 py-2 text-sm font-medium rounded-lg hover:bg-orange-200 transition-all {{ request()->routeIs('laporan') ? 'sidebar-active' : '' }}">
                                <i class="fas fa-chart-bar w-5 h-5 mr-3"></i>
                                <span>Laporan</span>
                            </a>

                            <a href="{{ route('grafik.penjualan') }}"
                                class="flex items-center px-3 py-2 text-sm font-medium rounded-lg hover:bg-orange-200 transition-all {{ request()->routeIs('grafik.penjualan') ? 'sidebar-active' : '' }}">
                                <i class="fas fa-chart-line w-5 h-5 mr-3"></i>
                                <span>Grafik Penjualan</span>
                            </a>
                        @endif

                        {{-- Menu untuk PEMILIK --}}
                        @if (auth()->user()->level === 'pemilik')
                            <a href="{{ route('laporan') }}"
                                class="flex items-center px-3 py-2 text-sm font-medium rounded-lg hover:bg-orange-200 transition-all {{ request()->routeIs('laporan') ? 'sidebar-active' : '' }}">
                                <i class="fas fa-chart-bar w-5 h-5 mr-3"></i>
                                <span>Laporan</span>
                            </a>

                            <a href="{{ route('grafik.penjualan') }}"
                                class="flex items-center px-3 py-2 text-sm font-medium rounded-lg hover:bg-orange-200 transition-all {{ request()->routeIs('grafik.penjualan') ? 'sidebar-active' : '' }}">
                                <i class="fas fa-chart-line w-5 h-5 mr-3"></i>
                                <span>Grafik Penjualan</span>
                            </a>
                        @endif

                    </div>
                </nav>

                <!-- Tombol Logout -->
                <div class="p-4 border-t border-orange-200 bg-orange-50">
                    <div class="flex items-center px-3 py-2 rounded-md hover:bg-orange-100 transition text-sm text-orange-700 font-medium cursor-pointer"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <svg class="w-5 h-5 mr-2 text-orange-700" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />
                        </svg>
                        Keluar
                    </div>

                    <!-- Form logout tersembunyi -->
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                </div>


            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex flex-col flex-1 md:ml-64">
            <!-- Top Navbar -->
            <header class="sticky top-0 z-40 bg-white border-b border-gray-200 shadow-sm">
                <div class="flex items-center justify-between h-16 px-4">
                    <!-- Mobile menu button -->
                    <button id="openSidebarBtn"
                        class="p-1 mr-4 text-gray-500 rounded-md md:hidden hover:text-gray-900 hover:bg-gray-100 focus:outline-none">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <div></div>
                    <!-- Right side navbar items -->
                    <div class="flex items-center space-x-4">
                        <!-- Profile dropdown -->
                        <div class="relative">
                            <button id="profileDropdownBtn"
                                class="flex items-center max-w-xs text-sm bg-gray-800 rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-white">
                                <span class="sr-only">Open user menu</span>
                                <img class="h-8 w-8 rounded-full"
                                    src="https://ui-avatars.com/api/?name=Admin+User&background=FFEDD5&color=DC2626&size=256"
                                    alt="User avatar">
                            </button>

                            <!-- Dropdown menu kosong (kalau mau isi info user atau lainnya nanti) -->
                            <div id="profileDropdown"
                                class="absolute right-0 w-48 py-1 mt-2 bg-white rounded-md shadow-lg hidden ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
                                <div class="block px-4 py-2 text-sm text-gray-500">
                                    Logged in as {{ auth()->user()->username }}
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </header>

            <!-- Page Content -->
            <main class="">
                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- Overlay for mobile sidebar -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-gray-600 bg-opacity-50 z-40 hidden md:hidden"></div>

    <script>
        // Toggle sidebar on mobile
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const openSidebarBtn = document.getElementById('openSidebarBtn');
        const closeSidebarBtn = document.getElementById('closeSidebarBtn');

        // Open sidebar
        openSidebarBtn.addEventListener('click', () => {
            sidebar.classList.remove('-translate-x-full');
            sidebarOverlay.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        });

        // Close sidebar
        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            sidebarOverlay.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        closeSidebarBtn.addEventListener('click', closeSidebar);
        sidebarOverlay.addEventListener('click', closeSidebar);

        // Profile dropdown toggle
        const profileDropdownBtn = document.getElementById('profileDropdownBtn');
        const profileDropdown = document.getElementById('profileDropdown');

        profileDropdownBtn.addEventListener('click', () => {
            profileDropdown.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (event) => {
            if (!profileDropdownBtn.contains(event.target) && !profileDropdown.contains(event.target)) {
                profileDropdown.classList.add('hidden');
            }
        });

        // Handle window resize
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768) { // md breakpoint
                sidebar.classList.remove('-translate-x-full');
                sidebarOverlay.classList.add('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
            }
        });
    </script>
    @stack('modals')
    <!-- Adds the Core Table Scripts -->
    @rappasoftTableScripts
    @stack('scripts')
    <!-- Adds any relevant Third-Party Scripts (e.g. Flatpickr) -->
    @rappasoftTableThirdPartyScripts
    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</body>

</html>
