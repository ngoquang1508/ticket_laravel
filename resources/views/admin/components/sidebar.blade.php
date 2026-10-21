<!-- Sidebar -->
<aside id="sidebar" class="w-64 bg-gray-900 text-white flex flex-col shadow-2xl shrink-0">
    <div id="sidebar-logo-container"
        class="flex items-center h-16 shrink-0 border-b border-gray-800 px-5 gap-3 overflow-hidden transition-all duration-300">
        <div
            class="flex items-center justify-center h-8 w-8 rounded-lg bg-primary-600 text-white font-bold shrink-0 shadow-md shadow-primary-600/30">
            T
        </div>
        <a href="{{ route('admin.dashboard') }}"
            class="sidebar-logo-text text-lg font-bold tracking-tight whitespace-nowrap overflow-hidden text-gray-100 hover:text-white transition-colors">Ticket
            Admin</a>
    </div>

    <nav class="flex-1 overflow-y-auto overflow-x-hidden py-4 space-y-2 px-3 custom-scrollbar">
        <!-- Dashboard -->
        <a href="{{ route('admin.dashboard') }}"
            class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-primary-600 text-white shadow-md shadow-primary-600/20' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}"
            title="Dashboard">
            <x-icons.chart-bar class="sidebar-icon h-5 w-5 shrink-0" />
            <span class="sidebar-text whitespace-nowrap">Dashboard</span>
        </a>

        <!-- Địa điểm -->
        <a href="{{ route('admin.locations.index') }}"
            class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.locations.*') ? 'bg-primary-600 text-white shadow-md shadow-primary-600/20' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}"
            title="Địa điểm">
            <x-icons.location class="sidebar-icon h-5 w-5 shrink-0" />
            <span class="sidebar-text whitespace-nowrap">Địa điểm</span>
        </a>

        <!-- Danh mục -->
        <a href="{{ route('admin.categories.index') }}"
            class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.categories.*') ? 'bg-primary-600 text-white shadow-md shadow-primary-600/20' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}"
            title="Danh mục">
            <x-icons.tag class="sidebar-icon h-5 w-5 shrink-0" />
            <span class="sidebar-text whitespace-nowrap">Danh mục</span>
        </a>

        <!-- Sự kiện -->
        <a href="{{ route('admin.events.index') }}"
            class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.events.*') ? 'bg-primary-600 text-white shadow-md shadow-primary-600/20' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}"
            title="Sự kiện">
            <x-icons.calendar class="sidebar-icon h-5 w-5 shrink-0" />
            <span class="sidebar-text whitespace-nowrap">Sự kiện</span>
        </a>

        <!-- Đơn hàng -->
        <a href="{{ route('admin.orders.index') }}"
            class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.orders.*') ? 'bg-primary-600 text-white shadow-md shadow-primary-600/20' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}"
            title="Đơn hàng">
            <x-icons.shopping-bag class="sidebar-icon h-5 w-5 shrink-0" />
            <span class="sidebar-text whitespace-nowrap">Đơn hàng</span>
        </a>

        <!-- Người dùng -->
        <a href="{{ route('admin.users.index') }}"
            class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.users.*') ? 'bg-primary-600 text-white shadow-md shadow-primary-600/20' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}"
            title="Người dùng">
            <x-icons.user class="sidebar-icon h-5 w-5 shrink-0" />
            <span class="sidebar-text whitespace-nowrap">Người dùng</span>
        </a>

        <!-- Check-in vé -->
        <a href="{{ route('admin.checkin.index') }}"
            class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.checkin.*') ? 'bg-primary-600 text-white shadow-md shadow-primary-600/20' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}"
            title="Check-in vé">
            <x-icons.qrcode class="sidebar-icon h-5 w-5 shrink-0" />
            <span class="sidebar-text whitespace-nowrap">Check-in vé</span>
        </a>

        <!-- Border separator -->
        <div class="h-px w-full bg-gray-800 my-4"></div>

        <!-- Xem website -->
        <a href="{{ route('home') }}"
            class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium transition-all duration-200 text-gray-400 hover:bg-gray-800 hover:text-white"
            title="Xem website">
            <x-icons.globe class="sidebar-icon h-5 w-5 shrink-0" />
            <span class="sidebar-text whitespace-nowrap">Xem website</span>
        </a>
    </nav>

    <div class="p-4 border-t border-gray-800 shrink-0">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button
                class="w-full flex items-center gap-3 rounded-xl bg-gray-800/50 px-3 py-3 text-sm font-medium text-gray-300 hover:bg-red-500/10 hover:text-red-400 transition-all duration-200 border border-transparent hover:border-red-500/20 group"
                title="Đăng xuất">
                <x-icons.logout class="sidebar-icon h-5 w-5 shrink-0 group-hover:rotate-12 transition-transform" />
                <span id="logout-text" class="sidebar-text whitespace-nowrap">Đăng xuất</span>
            </button>
        </form>
    </div>
</aside>