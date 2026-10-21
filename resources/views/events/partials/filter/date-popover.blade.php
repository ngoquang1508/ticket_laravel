{{--
    Component Popover Lọc Thời Gian & Lịch 2 Tháng (Ticketbox Style)
--}}

<div id="popover-date-filter" style="color-scheme: light;"
    class="hidden fixed top-24 left-3 right-3 sm:left-auto sm:right-0 sm:top-full sm:absolute sm:mt-2 z-50 bg-white text-gray-800 rounded-2xl shadow-2xl border border-gray-100 p-4 sm:p-5 w-auto sm:w-[650px] max-w-[calc(100vw-1.5rem)] sm:max-w-none max-h-[85vh] overflow-y-auto">

    {{-- Tiêu đề và nút Đóng trên Mobile --}}
    <div class="flex items-center justify-between pb-3 mb-3 border-b border-gray-100 sm:hidden">
        <span class="font-bold text-gray-900 text-sm">Thời gian</span>
        <button type="button" class="btn-close-popover p-1 text-gray-400 hover:text-gray-600 cursor-pointer">
            <x-icons.close class="w-5 h-5" />
        </button>
    </div>

    {{-- Cụm nút chọn nhanh ngày (Presets) --}}
    <div class="flex items-center gap-2 overflow-x-auto pb-1 no-scrollbar">
        @php
            $activePreset = $hasDateFilter ? ($datePreset ?? 'custom') : 'all';
        @endphp
        <button type="button"
            class="btn-date-preset px-3 py-1.5 rounded-lg border text-xs font-semibold transition cursor-pointer shrink-0 {{ $activePreset === 'all' ? 'border-emerald-500 text-emerald-600 bg-emerald-50/70' : 'border-gray-200 text-gray-700 hover:border-emerald-500' }}"
            data-preset="all">Tất cả các ngày</button>
        <button type="button"
            class="btn-date-preset px-3 py-1.5 rounded-lg border text-xs font-semibold transition cursor-pointer shrink-0 {{ $activePreset === 'today' ? 'border-emerald-500 text-emerald-600 bg-emerald-50/70' : 'border-gray-200 text-gray-700 hover:border-emerald-500' }}"
            data-preset="today">Hôm nay</button>
        <button type="button"
            class="btn-date-preset px-3 py-1.5 rounded-lg border text-xs font-semibold transition cursor-pointer shrink-0 {{ $activePreset === 'tomorrow' ? 'border-emerald-500 text-emerald-600 bg-emerald-50/70' : 'border-gray-200 text-gray-700 hover:border-emerald-500' }}"
            data-preset="tomorrow">Ngày mai</button>
        <button type="button"
            class="btn-date-preset px-3 py-1.5 rounded-lg border text-xs font-semibold transition cursor-pointer shrink-0 {{ $activePreset === 'weekend' ? 'border-emerald-500 text-emerald-600 bg-emerald-50/70' : 'border-gray-200 text-gray-700 hover:border-emerald-500' }}"
            data-preset="weekend">Cuối tuần này</button>
        <button type="button"
            class="btn-date-preset px-3 py-1.5 rounded-lg border text-xs font-semibold transition cursor-pointer shrink-0 {{ $activePreset === 'month' ? 'border-emerald-500 text-emerald-600 bg-emerald-50/70' : 'border-gray-200 text-gray-700 hover:border-emerald-500' }}"
            data-preset="month">Tháng này</button>
    </div>

    {{-- Phân cách nét đứt --}}
    <div class="border-b border-dashed border-gray-200 my-4"></div>

    {{-- Khung Lịch 2 Tháng Song Song --}}
    <div class="relative">
        {{-- Nút điều hướng tháng --}}
        <div class="flex items-center justify-between mb-2">
            <button type="button" id="btn-prev-month"
                class="p-1.5 rounded-full hover:bg-gray-100 text-gray-600 transition cursor-pointer"
                title="Tháng trước">
                <x-icons.chevron-left class="w-5 h-5" />
            </button>
            <div class="flex-1 flex justify-around text-sm font-bold text-gray-800">
                <span id="label-month-left">Tháng ..., 2026</span>
                <span id="label-month-right" class="hidden sm:inline">Tháng ..., 2026</span>
            </div>
            <button type="button" id="btn-next-month"
                class="p-1.5 rounded-full hover:bg-gray-100 text-gray-600 transition cursor-pointer"
                title="Tháng sau">
                <x-icons.chevron-right class="w-5 h-5" />
            </button>
        </div>

        {{-- Khung lưới 2 lịch (1 tháng trên mobile, 2 tháng trên tablet/desktop) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
            {{-- Lịch tháng bên trái --}}
            <div id="calendar-left-container">
                <div class="grid grid-cols-7 text-center text-xs font-semibold text-gray-400 py-1 mb-1">
                    <span>T2</span><span>T3</span><span>T4</span><span>T5</span><span>T6</span><span>T7</span><span>CN</span>
                </div>
                <div class="grid grid-cols-7 gap-y-1 gap-x-0 text-center text-xs sm:text-sm" id="days-grid-left">
                </div>
            </div>

            {{-- Lịch tháng bên phải --}}
            <div id="calendar-right-container" class="hidden sm:block">
                <div class="grid grid-cols-7 text-center text-xs font-semibold text-gray-400 py-1 mb-1">
                    <span>T2</span><span>T3</span><span>T4</span><span>T5</span><span>T6</span><span>T7</span><span>CN</span>
                </div>
                <div class="grid grid-cols-7 gap-y-1 gap-x-0 text-center text-xs sm:text-sm" id="days-grid-right">
                </div>
            </div>
        </div>
    </div>

    {{-- Phân cách nét đứt --}}
    <div class="border-b border-dashed border-gray-200 my-4"></div>

    {{-- Nút Chân Popover Lịch --}}
    <div class="flex items-center justify-between gap-3">
        <button type="button" id="btn-reset-date"
            class="px-5 py-2.5 rounded-xl border border-emerald-500 text-emerald-600 hover:bg-emerald-50 font-semibold text-xs sm:text-sm transition cursor-pointer">
            Thiết lập lại
        </button>
        <button type="button" id="btn-apply-date"
            class="px-8 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs sm:text-sm shadow-md shadow-emerald-500/20 transition cursor-pointer">
            Áp dụng
        </button>
    </div>
</div>
