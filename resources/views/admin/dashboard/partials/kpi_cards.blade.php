{{-- 3 Chỉ số KPI cốt lõi: Doanh thu thực tế, Số đơn hàng, Số vé đã bán --}}
<div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
    {{-- Tổng doanh thu thực tế --}}
    <div
        class="relative overflow-hidden rounded-2xl border border-blue-200/80 bg-white p-5 shadow-sm transition hover:shadow-md">
        <div class="flex items-center justify-between">
            <span class="text-xs font-semibold uppercase tracking-wider text-blue-700">Tổng doanh thu thực tế</span>
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </span>
        </div>
        <div class="mt-3">
            <div class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">
                {{ number_format($totalRevenue, 0, ',', '.') }} <span
                    class="text-lg font-medium text-gray-500">₫</span>
            </div>
            <div class="mt-2 flex items-center gap-1.5 text-xs text-emerald-600">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>Chỉ tính đơn thanh toán thành công (paid)</span>
            </div>
        </div>
        <div class="absolute bottom-0 left-0 right-0 h-1 bg-blue-600"></div>
    </div>

    {{-- Số đơn hàng --}}
    <div
        class="relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:shadow-md">
        <div class="flex items-center justify-between">
            <span class="text-xs font-semibold uppercase tracking-wider text-gray-600">Số đơn hàng</span>
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </span>
        </div>
        <div class="mt-3">
            <div class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">
                {{ number_format($totalOrders) }} <span class="text-lg font-medium text-gray-500">đơn</span>
            </div>
            <div class="mt-2 flex items-center justify-between text-xs text-gray-500">
                <span>Đã thanh toán: <strong
                        class="text-gray-900">{{ $orderStatusStats['paid']['count'] }}</strong></span>
                <span>Tỷ lệ: <strong
                        class="text-emerald-600">{{ $orderStatusStats['paid']['percentage'] }}%</strong></span>
            </div>
        </div>
        <div class="absolute bottom-0 left-0 right-0 h-1 bg-indigo-600"></div>
    </div>

    {{-- Số vé đã bán --}}
    <div
        class="relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:shadow-md">
        <div class="flex items-center justify-between">
            <span class="text-xs font-semibold uppercase tracking-wider text-gray-600">Số vé đã bán</span>
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                </svg>
            </span>
        </div>
        <div class="mt-3">
            <div class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">
                {{ number_format($totalTicketsSold) }} <span class="text-lg font-medium text-gray-500">vé</span>
            </div>
            <div class="mt-2 flex items-center gap-1.5 text-xs text-gray-500">
                <svg class="h-3.5 w-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Tính từ các đơn thanh toán thành công</span>
            </div>
        </div>
        <div class="absolute bottom-0 left-0 right-0 h-1 bg-emerald-600"></div>
    </div>
</div>
