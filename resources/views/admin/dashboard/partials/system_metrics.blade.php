{{-- Thống kê tổng quan tài nguyên hệ thống --}}
<div>
    <div class="mb-3 flex items-center justify-between">
        <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500">Tổng quan tài nguyên hệ thống</h3>
    </div>
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
        @php
            $systemCards = [
                ['Sự kiện', $systemMetrics['events'], 'admin.events.index'],
                ['Đã đăng', $systemMetrics['publishedEvents'], 'admin.events.index'],
                ['Địa điểm', $systemMetrics['locations'], 'admin.locations.index'],
                ['Danh mục', $systemMetrics['categories'], 'admin.categories.index'],
                ['Loại vé', $systemMetrics['ticketTypes'], 'admin.events.index'],
                ['Đơn hàng', $systemMetrics['orders'], 'admin.orders.index'],
            ];
        @endphp
        @foreach ($systemCards as [$label, $val, $route])
            <a href="{{ route($route) }}"
                class="group rounded-2xl border border-gray-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-gray-300 hover:shadow-md">
                <span class="text-xs font-medium text-gray-500">{{ $label }}</span>
                <p class="mt-1.5 text-2xl font-bold tracking-tight text-gray-900">{{ number_format($val) }}</p>
                <span class="mt-2 inline-flex items-center text-xs font-medium text-primary-600 group-hover:underline">
                    Chi tiết →
                </span>
            </a>
        @endforeach
    </div>
</div>
