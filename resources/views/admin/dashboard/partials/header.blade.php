{{-- Tiêu đề trang & Nút thao tác nhanh trên cùng --}}
<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-gray-900">Dashboard</h1>
        <p class="mt-1 text-sm text-gray-500">Tổng quan dữ liệu kinh doanh, tình hình bán vé và doanh thu thực tế.</p>
    </div>
    <div class="flex flex-wrap items-center gap-2.5">
        <a href="{{ route('admin.orders.index') }}"
            class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-xs transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-400">
            <x-icons.shopping-bag class="h-4 w-4 text-gray-500" />
            Quản lý đơn hàng
        </a>
        <a href="{{ route('admin.events.create') }}"
            class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-xs transition hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
            <x-icons.plus class="h-4 w-4" />
            Tạo sự kiện
        </a>
    </div>
</div>
