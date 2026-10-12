{{-- Khu vực biểu đồ (2 Cột: Biểu đồ Doanh thu & Top sự kiện bán chạy nhất) --}}
<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    {{-- Biểu đồ 1: Doanh thu theo thời gian (Line/Area Chart ApexCharts) --}}
    <div class="flex flex-col rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
            <div>
                <h2 class="text-base font-bold text-gray-900">Doanh thu theo thời gian</h2>
                <p class="text-xs text-gray-500">Diễn biến dòng tiền từ các đơn thanh toán thành công (VND)</p>
            </div>
            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                </svg>
            </span>
        </div>
        <div class="mt-4 flex-1">
            <div id="revenueLineChart" class="w-full min-h-[290px]"></div>
        </div>
    </div>

    {{-- Biểu đồ 2: Top sự kiện bán chạy nhất --}}
    <div class="flex flex-col rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
            <div>
                <h2 class="text-base font-bold text-gray-900">Top sự kiện bán chạy nhất</h2>
                <p class="text-xs text-gray-500">Số lượng vé bán ra thành công nhiều nhất</p>
            </div>
            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </span>
        </div>

        <div class="mt-4 flex-1 flex flex-col justify-center">
            @if ($topEvents->isNotEmpty())
                <div class="space-y-4 py-2">
                    @foreach ($topEvents as $ev)
                        <div class="group flex items-center gap-3">
                            {{-- Tên sự kiện bên trái --}}
                            <div class="w-36 sm:w-44 shrink-0 truncate text-sm font-medium text-gray-800 group-hover:text-primary-700 transition"
                                title="{{ $ev['name'] }}">
                                {{ $ev['name'] }}
                            </div>

                            {{-- Thanh bar tiến trình ở giữa --}}
                            <div class="relative flex-1 h-7 rounded-md bg-gray-100 overflow-hidden">
                                {{-- Thanh bar xanh ngọc --}}
                                <div class="relative h-full bg-emerald-500 rounded-md transition-all duration-700 ease-out group-hover:bg-emerald-600"
                                    style="width: {{ max($ev['percentage'], 6) }}%;">
                                </div>
                            </div>

                            {{-- Số vé bán hiển thị bên phải --}}
                            <div
                                class="w-10 shrink-0 text-right text-sm font-bold text-gray-900 group-hover:text-emerald-700 transition">
                                {{ $ev['tickets'] }} vé
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                {{-- Trạng thái rỗng khi chưa có vé bán --}}
                <div class="flex h-[260px] flex-col items-center justify-center text-center">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                    </div>
                    <p class="mt-2 text-sm font-medium text-gray-900">Chưa có vé bán</p>
                    <p class="text-xs text-gray-500">Chưa có sự kiện nào phát sinh đơn hàng đã thanh toán trong kỳ này.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
