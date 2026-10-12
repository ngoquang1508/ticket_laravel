{{-- Khu vực Đơn hàng theo trạng thái: Chờ thanh toán, Đã thanh toán, Đã hủy --}}
<div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b border-gray-100 pb-4">
        <div>
            <h2 class="text-base font-bold text-gray-900">Phân bố đơn hàng theo trạng thái</h2>
            <p class="text-xs text-gray-500">Theo dõi tỷ lệ thanh toán và xử lý đơn hàng trong kỳ lọc hiện tại.</p>
        </div>
        <span
            class="inline-flex items-center rounded-lg bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700">
            Tổng: {{ number_format($totalOrders) }} đơn
        </span>
    </div>

    {{-- Thanh tiến trình tổng thể (Progress Bar) --}}
    <div class="mt-4">
        <div class="flex h-3 w-full overflow-hidden rounded-full bg-gray-100">
            @if ($totalOrders > 0)
                <div class="bg-emerald-500 transition-all duration-500"
                    style="width: {{ $orderStatusStats['paid']['percentage'] }}%"
                    title="Đã thanh toán: {{ $orderStatusStats['paid']['percentage'] }}%"></div>
                <div class="bg-amber-400 transition-all duration-500"
                    style="width: {{ $orderStatusStats['pending']['percentage'] }}%"
                    title="Chờ thanh toán: {{ $orderStatusStats['pending']['percentage'] }}%"></div>
                <div class="bg-rose-500 transition-all duration-500"
                    style="width: {{ $orderStatusStats['cancelled']['percentage'] }}%"
                    title="Đã hủy: {{ $orderStatusStats['cancelled']['percentage'] }}%"></div>
            @else
                <div class="w-full bg-gray-200"></div>
            @endif
        </div>
    </div>

    {{-- 3 Thẻ chi tiết từng trạng thái --}}
    <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
        {{-- Đã thanh toán --}}
        <div class="flex items-center justify-between rounded-xl border border-emerald-100 bg-emerald-50/50 p-3.5">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M5 13l4 4L19 7" />
                    </svg>
                </span>
                <div>
                    <p class="text-xs font-medium text-emerald-800">Đã thanh toán</p>
                    <p class="text-lg font-bold text-emerald-900">
                        {{ number_format($orderStatusStats['paid']['count']) }} <span
                            class="text-xs font-normal text-emerald-700">đơn</span>
                    </p>
                </div>
            </div>
            <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-800">
                {{ $orderStatusStats['paid']['percentage'] }}%
            </span>
        </div>

        {{-- Chờ thanh toán --}}
        <div class="flex items-center justify-between rounded-xl border border-amber-100 bg-amber-50/50 p-3.5">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-100 text-amber-700">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
                <div>
                    <p class="text-xs font-medium text-amber-800">Chờ thanh toán</p>
                    <p class="text-lg font-bold text-amber-900">
                        {{ number_format($orderStatusStats['pending']['count']) }} <span
                            class="text-xs font-normal text-amber-700">đơn</span>
                    </p>
                </div>
            </div>
            <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-800">
                {{ $orderStatusStats['pending']['percentage'] }}%
            </span>
        </div>

        {{-- Đã hủy --}}
        <div class="flex items-center justify-between rounded-xl border border-rose-100 bg-rose-50/50 p-3.5">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-rose-100 text-rose-700">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </span>
                <div>
                    <p class="text-xs font-medium text-rose-800">Đã hủy / Hết hạn</p>
                    <p class="text-lg font-bold text-rose-900">
                        {{ number_format($orderStatusStats['cancelled']['count']) }} <span
                            class="text-xs font-normal text-rose-700">đơn</span>
                    </p>
                </div>
            </div>
            <span class="rounded-full bg-rose-100 px-2 py-0.5 text-xs font-semibold text-rose-800">
                {{ $orderStatusStats['cancelled']['percentage'] }}%
            </span>
        </div>
    </div>
</div>
