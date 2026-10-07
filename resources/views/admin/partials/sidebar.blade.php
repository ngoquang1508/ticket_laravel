<!-- Sidebar -->
<aside id="sidebar" class="w-64 bg-gray-900 text-white flex flex-col shadow-2xl shrink-0">
    <div id="sidebar-logo-container" class="flex items-center h-16 shrink-0 border-b border-gray-800 px-5 gap-3 overflow-hidden transition-all duration-300">
        <div class="flex items-center justify-center h-8 w-8 rounded-lg bg-primary-600 text-white font-bold shrink-0 shadow-md shadow-primary-600/30">
            T
        </div>
        <a href="{{ route('admin.dashboard') }}" class="sidebar-logo-text text-lg font-bold tracking-tight whitespace-nowrap overflow-hidden text-gray-100 hover:text-white transition-colors">Ticket Admin</a>
    </div>

    <nav class="flex-1 overflow-y-auto overflow-x-hidden py-4 space-y-2 px-3 custom-scrollbar">
        <!-- Dashboard -->
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-primary-600 text-white shadow-md shadow-primary-600/20' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}" title="Dashboard">
            <svg class="sidebar-icon h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
            </svg>
            <span class="sidebar-text whitespace-nowrap">Dashboard</span>
        </a>
        
        <!-- Địa điểm -->
        <a href="{{ route('admin.locations.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.locations.*') ? 'bg-primary-600 text-white shadow-md shadow-primary-600/20' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}" title="Địa điểm">
            <svg class="sidebar-icon h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
            </svg>
            <span class="sidebar-text whitespace-nowrap">Địa điểm</span>
        </a>

        <!-- Danh mục -->
        <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.categories.*') ? 'bg-primary-600 text-white shadow-md shadow-primary-600/20' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}" title="Danh mục">
            <svg class="sidebar-icon h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
            </svg>
            <span class="sidebar-text whitespace-nowrap">Danh mục</span>
        </a>

        <!-- Sự kiện -->
        <a href="{{ route('admin.events.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.events.*') ? 'bg-primary-600 text-white shadow-md shadow-primary-600/20' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}" title="Sự kiện">
            <svg class="sidebar-icon h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" />
            </svg>
            <span class="sidebar-text whitespace-nowrap">Sự kiện</span>
        </a>

        <!-- Border separator -->
        <div class="h-px w-full bg-gray-800 my-4"></div>

        <!-- Xem website -->
        <a href="{{ route('home') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium transition-all duration-200 text-gray-400 hover:bg-gray-800 hover:text-white" title="Xem website">
            <svg class="sidebar-icon h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" />
            </svg>
            <span class="sidebar-text whitespace-nowrap">Xem website</span>
        </a>
    </nav>

    <div class="p-4 border-t border-gray-800 shrink-0">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="w-full flex items-center gap-3 rounded-xl bg-gray-800/50 px-3 py-3 text-sm font-medium text-gray-300 hover:bg-red-500/10 hover:text-red-400 transition-all duration-200 border border-transparent hover:border-red-500/20 group" title="Đăng xuất">
                <svg class="sidebar-icon h-5 w-5 shrink-0 group-hover:rotate-12 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                </svg>
                <span id="logout-text" class="sidebar-text whitespace-nowrap">Đăng xuất</span>
            </button>
        </form>
    </div>
</aside>
