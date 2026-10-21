{{-- Các thẻ thống kê nhanh --}}
<div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-2 lg:grid-cols-5">
    {{-- Tổng đơn hàng --}}
    <div class="rounded-xl border border-gray-200/80 bg-white p-4.5 shadow-2xs transition hover:shadow-xs">
        <div class="flex items-center justify-between">
            <span class="text-xs font-medium uppercase tracking-wider text-gray-500">Tổng đơn hàng</span>
            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100 text-gray-600">
                <x-icons.shopping-bag class="h-4 w-4" />
            </span>
        </div>
        <p class="mt-3 text-2xl font-bold text-gray-900">{{ number_format($metrics['total']) }}</p>
        <span class="mt-1 block text-xs text-gray-400">Tất cả thời gian</span>
    </div>

    {{-- Doanh thu hoàn tất --}}
    <div class="rounded-xl border border-emerald-200/60 bg-emerald-50/30 p-4.5 shadow-2xs transition hover:shadow-xs">
        <div class="flex items-center justify-between">
            <span class="text-xs font-medium uppercase tracking-wider text-emerald-700">Doanh thu đã thu</span>
            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700">
                <x-icons.currency-dollar class="h-4 w-4" />
            </span>
        </div>
        <p class="mt-3 text-2xl font-bold text-emerald-700">{{ number_format($metrics['revenue'], 0, ',', '.') }}đ</p>
        <span class="mt-1 block text-xs text-emerald-600">Đơn thành công</span>
    </div>

    {{-- Hoàn tất --}}
    <div class="rounded-xl border border-gray-200/80 bg-white p-4.5 shadow-2xs transition hover:shadow-xs">
        <div class="flex items-center justify-between">
            <span class="text-xs font-medium uppercase tracking-wider text-gray-500">Đã thanh toán</span>
            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-green-100 text-green-600">
                <x-icons.check class="h-4 w-4" />
            </span>
        </div>
        <p class="mt-3 text-2xl font-bold text-gray-900">{{ number_format($metrics['completed']) }}</p>
        <span class="mt-1 block text-xs text-gray-400">Giao dịch thành công</span>
    </div>

    {{-- Chờ xử lý --}}
    <div class="rounded-xl border border-gray-200/80 bg-white p-4.5 shadow-2xs transition hover:shadow-xs">
        <div class="flex items-center justify-between">
            <span class="text-xs font-medium uppercase tracking-wider text-gray-500">Chờ thanh toán</span>
            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-100 text-amber-600">
                <x-icons.clock class="h-4 w-4" />
            </span>
        </div>
        <p class="mt-3 text-2xl font-bold text-gray-900">{{ number_format($metrics['pending']) }}</p>
        <span class="mt-1 block text-xs text-gray-400">Đang giữ chỗ</span>
    </div>

    {{-- Đã hủy --}}
    <div class="rounded-xl border border-gray-200/80 bg-white p-4.5 shadow-2xs transition hover:shadow-xs col-span-2 sm:col-span-1">
        <div class="flex items-center justify-between">
            <span class="text-xs font-medium uppercase tracking-wider text-gray-500">Đã hủy / Quá hạn</span>
            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-100 text-red-600">
                <x-icons.close class="h-4 w-4" />
            </span>
        </div>
        <p class="mt-3 text-2xl font-bold text-gray-900">{{ number_format($metrics['cancelled']) }}</p>
        <span class="mt-1 block text-xs text-gray-400">Vé đã được hoàn trả</span>
    </div>
</div>
