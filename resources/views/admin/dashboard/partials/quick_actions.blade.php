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
                <x-icons.shopping-bag class="h-4 w-4 text-gray-500" />
                Quản lý đơn hàng
            </span>
            <span class="text-gray-400">→</span>
        </a>
        <a href="{{ route('admin.events.create') }}"
            class="flex items-center justify-between rounded-xl border border-gray-200/80 p-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
            <span class="flex items-center gap-2.5">
                <x-icons.plus class="h-4 w-4 text-gray-500" />
                Tạo sự kiện mới
            </span>
            <span class="text-gray-400">+</span>
        </a>
        <a href="{{ route('admin.locations.create') }}"
            class="flex items-center justify-between rounded-xl border border-gray-200/80 p-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
            <span class="flex items-center gap-2.5">
                <x-icons.location class="h-4 w-4 text-gray-500" />
                Thêm địa điểm tổ chức
            </span>
            <span class="text-gray-400">+</span>
        </a>
        <a href="{{ route('admin.categories.create') }}"
            class="flex items-center justify-between rounded-xl border border-gray-200/80 p-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
            <span class="flex items-center gap-2.5">
                <x-icons.tag class="h-4 w-4 text-gray-500" />
                Thêm danh mục sự kiện
            </span>
            <span class="text-gray-400">+</span>
        </a>
    </div>
</section>
