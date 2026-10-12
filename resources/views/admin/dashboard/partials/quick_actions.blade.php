{{-- Thao tác nhanh --}}
<section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
    <div class="border-b border-gray-100 pb-3">
        <h2 class="font-bold text-gray-900">Thao tác nhanh</h2>
        <p class="text-xs text-gray-500">Lối tắt quản trị thường dùng</p>
    </div>
    <div class="mt-4 space-y-2.5">
        <a href="{{ route('admin.orders.index') }}"
            class="flex items-center justify-between rounded-xl border border-gray-200/80 bg-gray-50/50 p-3 text-sm font-semibold text-gray-800 transition hover:border-primary-300 hover:bg-primary-50/50 hover:text-primary-700">
            <span class="flex items-center gap-2.5">
                <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                Quản lý đơn hàng
            </span>
            <span class="text-gray-400">→</span>
        </a>
        <a href="{{ route('admin.events.create') }}"
            class="flex items-center justify-between rounded-xl border border-gray-200/80 p-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
            <span class="flex items-center gap-2.5">
                <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Tạo sự kiện mới
            </span>
            <span class="text-gray-400">+</span>
        </a>
        <a href="{{ route('admin.locations.create') }}"
            class="flex items-center justify-between rounded-xl border border-gray-200/80 p-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
            <span class="flex items-center gap-2.5">
                <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                </svg>
                Thêm địa điểm tổ chức
            </span>
            <span class="text-gray-400">+</span>
        </a>
        <a href="{{ route('admin.categories.create') }}"
            class="flex items-center justify-between rounded-xl border border-gray-200/80 p-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
            <span class="flex items-center gap-2.5">
                <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
                Thêm danh mục sự kiện
            </span>
            <span class="text-gray-400">+</span>
        </a>
    </div>
</section>
