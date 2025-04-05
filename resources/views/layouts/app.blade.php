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
</head>

<body class="bg-gray-50">
    <div>
        <!-- Sidebar -->
        <aside id="sidebar"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-sidebar border-r border-sidebar-border transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">
            <div class="flex flex-col h-full">
                <!-- Logo -->
                <div class="flex items-center justify-between h-16 px-4 border-b border-sidebar-border">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 text-orange-500" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L2 7L12 12L22 7L12 2Z" fill="currentColor" />
                            <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                        <span class="ml-2 text-lg font-semibold">Penjualan Parfum</span>
                    </div>
                    <button id="closeSidebarBtn" class="p-1 rounded-md md:hidden hover:bg-sidebar-accent">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-2 py-4 overflow-y-auto">
                    <div class="space-y-1">
                        <a href="{{ route('dashboard') }}"
                            class="flex items-center px-3 py-2.5 text-sm font-medium rounded-md group transition-all {{ activeSidebar('dashboard') }}">
                            <i class="fas fa-tachometer-alt w-5 h-5 mr-3"></i>
                            <span>Dashboard</span>
                        </a>

                        <!-- Inventory Management -->
                        <div class="pt-4">
                            <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Inventory
                            </h3>
                            <div class="mt-2 space-y-1">
                                <a href="{{ route('barang') }}"
                                    class="flex items-center px-3 py-2.5 text-sm font-medium rounded-md group transition-all {{ activeSidebar('barang') }}">
                                    <i class="fas fa-box w-5 h-5 mr-3"></i>
                                    <span>Barang</span>
                                </a>

                                <a href="{{ route('supplier') }}"
                                    class="flex items-center px-3 py-2.5 text-sm font-medium rounded-md group transition-all {{ activeSidebar('supplier') }}">
                                    <i class="fas fa-truck w-5 h-5 mr-3"></i>
                                    <span>Supplier</span>
                                </a>
                            </div>
                        </div>

                        <!-- Transactions -->
                        <div class="pt-4">
                            <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Transaksi
                            </h3>
                            <div class="mt-2 space-y-1">
                                <a href="{{ route('transaksi-pembelian') }}"
                                    class="flex items-center px-3 py-2.5 text-sm font-medium rounded-md group transition-all {{ activeSidebar('transaksi-pembelian') }}">
                                    <i class="fas fa-shopping-cart w-5 h-5 mr-3"></i>
                                    <span>Transaksi Pembelian</span>
                                </a>
                                <a href="{{ route('transaksi-penjualan') }}"
                                    class="flex items-center px-3 py-2.5 text-sm font-medium rounded-md group transition-all {{ activeSidebar('transaksi-penjualan') }}">
                                    <i class="fas fa-cash-register w-5 h-5 mr-3"></i>
                                    <span>Transaksi Penjualan</span>
                                </a>
                                <a href="{{ route('pajak-transaksi') }}"
                                    class="flex items-center px-3 py-2.5 text-sm font-medium rounded-md group transition-all {{ activeSidebar('pajak-transaksi') }}">
                                    <i class="fas fa-receipt w-5 h-5 mr-3"></i>
                                    <span>Pajak Transaksi</span>
                                </a>
                            </div>
                        </div>

                        <!-- Administration -->
                        <div class="pt-4">
                            <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Administrasi
                            </h3>
                            <div class="mt-2 space-y-1">
                                <a href="{{ route('user') }}"
                                    class="flex items-center px-3 py-2.5 text-sm font-medium rounded-md group transition-all {{ activeSidebar('user.*') }}">
                                    <i class="fas fa-users w-5 h-5 mr-3"></i>
                                    <span>User</span>
                                </a>

                            </div>
                        </div>
                    </div>
                </nav>


                <!-- User Profile -->
                <div class="p-4 border-t border-sidebar-border">
                    <div class="flex items-center">
                        <img class="h-9 w-9 rounded-full object-cover"
                            src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
                            alt="User avatar">
                        <div class="ml-3">
                            <p class="text-sm font-medium text-sidebar-foreground">Admin User</p>
                            <p class="text-xs text-gray-500">Administrator</p>
                        </div>
                    </div>
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
                                    src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
                                    alt="User avatar">
                            </button>

                            <!-- Dropdown menu -->
                            <div id="profileDropdown"
                                class="absolute right-0 w-48 py-1 mt-2 bg-white rounded-md shadow-lg hidden ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
                                <a href="#"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profil</a>
                                <a href="#"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Pengaturan</a>
                                <a href="#"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Keluar</a>
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

    <!-- Adds any relevant Third-Party Scripts (e.g. Flatpickr) -->
    @rappasoftTableThirdPartyScripts
    @livewireScripts
</body>

</html>
