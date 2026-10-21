{{-- Bộ lọc thống kê: Đơn giản, gọn gàng, đầy đủ tính năng --}}
<div class="rounded-2xl border border-gray-200 bg-white p-3.5 shadow-sm sm:p-4">
    <form method="GET" action="{{ route('admin.dashboard') }}" id="dashboardFilterForm"
        class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap items-center gap-2.5 flex-1 min-w-[280px]">
            {{-- Dropdown khoảng thời gian --}}
            <div class="relative min-w-[145px]">
                <select name="period" id="periodSelect" onchange="handlePeriodChange(this.value)"
                    class="w-full appearance-none rounded-xl border border-gray-300 bg-white py-2 pl-3 pr-8 text-xs font-semibold text-gray-800 shadow-2xs transition hover:border-gray-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                    <option value="today" {{ $period === 'today' ? 'selected' : '' }}>Hôm nay</option>
                    <option value="7days" {{ $period === '7days' ? 'selected' : '' }}>7 ngày qua</option>
                    <option value="30days" {{ $period === '30days' ? 'selected' : '' }}>30 ngày qua</option>
                    <option value="this_month" {{ $period === 'this_month' ? 'selected' : '' }}>Tháng này</option>
                    <option value="all" {{ $period === 'all' ? 'selected' : '' }}>Toàn thời gian</option>
                    <option value="custom" {{ $period === 'custom' ? 'selected' : '' }}>Tùy chọn ngày...</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                    <x-icons.chevron-down class="h-3.5 w-3.5" />
                </div>
            </div>

            {{-- Dropdown sự kiện --}}
            <div class="relative min-w-[160px] max-w-[240px] flex-1">
                <select name="event_id" id="eventSelect"
                    onchange="document.getElementById('dashboardFilterForm').submit()"
                    class="w-full appearance-none truncate rounded-xl border border-gray-300 bg-white py-2 pl-3 pr-8 text-xs font-semibold text-gray-800 shadow-2xs transition hover:border-gray-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                    <option value="">Tất cả sự kiện</option>
                    @foreach ($filterEvents as $ev)
                        <option value="{{ $ev->id }}" {{ (string) $eventId === (string) $ev->id ? 'selected' : '' }}>
                            {{ $ev->name }}
                        </option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                    <x-icons.chevron-down class="h-3.5 w-3.5" />
                </div>
            </div>

            {{-- Nhập ngày tùy chọn khi chọn custom --}}
            <div id="customDateRange"
                class="{{ $period === 'custom' ? 'flex' : 'hidden' }} items-center gap-1.5 flex-wrap">
                <input type="date" name="date_from" value="{{ $dateFrom }}"
                    class="rounded-xl border border-gray-300 bg-white px-2.5 py-1.5 text-xs text-gray-800 shadow-2xs focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                <span class="text-xs text-gray-400">-</span>
                <input type="date" name="date_to" value="{{ $dateTo }}"
                    class="rounded-xl border border-gray-300 bg-white px-2.5 py-1.5 text-xs text-gray-800 shadow-2xs focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                <button type="submit"
                    class="rounded-xl bg-gray-900 px-3 py-1.5 text-xs font-semibold text-white shadow-2xs transition hover:bg-gray-800">
                    Lọc
                </button>
            </div>
        </div>

        {{-- Nút Đặt lại khi có bộ lọc khác mặc định --}}
        @if ($period !== '30days' || $eventId || $dateFrom || $dateTo)
            <div class="flex items-center">
                <a href="{{ route('admin.dashboard') }}"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-gray-200 bg-gray-50 px-3 py-1.5 text-xs font-medium text-gray-600 transition hover:bg-gray-100 hover:text-gray-900"
                    title="Xóa bộ lọc về mặc định">
                    <x-icons.refresh class="h-3.5 w-3.5" />
                    Đặt lại
                </a>
            </div>
        @endif
    </form>
</div>
