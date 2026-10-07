<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Ticket System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Smooth transitions */
        #sidebar {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Desktop Sidebar Collapse (Mini mode) */
        @media (min-width: 1024px) {
            #sidebar {
                position: sticky;
                top: 0;
                height: 100vh;
            }

            body.sidebar-collapsed #sidebar {
                width: 5rem !important;
                /* 80px */
            }

            body.sidebar-collapsed .sidebar-text,
            body.sidebar-collapsed .sidebar-logo-text {
                display: none;
                opacity: 0;
            }

            body.sidebar-collapsed .sidebar-icon {
                margin: 0 auto;
            }

            body.sidebar-collapsed #logout-text {
                display: none;
            }

            body.sidebar-collapsed #sidebar-logo-container {
                justify-content: center;
                padding-left: 0;
                padding-right: 0;
            }
        }

        /* Mobile Overlay & Sidebar */
        #mobile-overlay {
            transition: opacity 0.3s ease;
            opacity: 0;
            pointer-events: none;
        }

        body.mobile-sidebar-open #mobile-overlay {
            opacity: 1;
            pointer-events: auto;
        }

        @media (max-width: 1023px) {
            #sidebar {
                position: fixed;
                top: 0;
                left: 0;
                height: 100vh;
                transform: translateX(-100%);
                z-index: 50;
            }

            body.mobile-sidebar-open #sidebar {
                transform: translateX(0);
            }
        }

        /* Custom scrollbar for sidebar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #374151;
            border-radius: 10px;
        }

        .custom-scrollbar:hover::-webkit-scrollbar-thumb {
            background: #4b5563;
        }
    </style>
</head>

<body class="min-h-screen bg-gray-50 text-gray-900 font-sans antialiased">
    <script>
        // Prevent FOUC (Flash of Unstyled Content) by setting state immediately
        if (window.innerWidth >= 1024 && localStorage.getItem('sidebar-collapsed') === 'true') {
            document.body.classList.add('sidebar-collapsed');
        }
    </script>

    @include('components.toast')

    <!-- Mobile overlay -->
    <div id="mobile-overlay" class="fixed inset-0 z-40 bg-gray-900/80 backdrop-blur-sm lg:hidden"
        onclick="toggleMobileSidebar()"></div>

    <div class="min-h-screen flex">
        @include('admin.partials.sidebar')

        <div id="main-content" class="min-w-0 flex-1 flex flex-col w-full">
            <header
                class="sticky top-0 z-20 flex h-16 shrink-0 items-center justify-between border-b bg-white/80 backdrop-blur-md px-4 shadow-sm sm:px-6">
                <div class="flex items-center gap-4">
                    <!-- Toggle Button -->
                    <button onclick="toggleSidebar()"
                        class="text-gray-500 hover:text-primary-600 focus:outline-none bg-gray-100 hover:bg-primary-50 p-2 rounded-xl transition-all duration-200">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-primary-600">Administration</p>
                        <p class="text-sm text-gray-500 hidden sm:block">
                            @yield('page_description', 'Quản lý hệ thống bán vé')
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-4 text-sm">
                    <div class="flex items-center gap-3">
                        <div
                            class="h-10 p-2 rounded-full bg-primary-100 border-2 border-white shadow-sm flex items-center justify-center text-primary-700 font-bold text-lg">
                            {{ auth()->user()->name }}
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 p-4 sm:p-6 lg:p-8 bg-gray-50/50">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        // Desktop toggle
        function toggleSidebar() {
            if (window.innerWidth >= 1024) {
                document.body.classList.toggle('sidebar-collapsed');
                localStorage.setItem('sidebar-collapsed', document.body.classList.contains('sidebar-collapsed'));
            } else {
                toggleMobileSidebar();
            }
        }

        // Mobile toggle
        function toggleMobileSidebar() {
            document.body.classList.toggle('mobile-sidebar-open');
        }
    </script>
    @stack('scripts')
</body>

</html>