{{-- Khối Chọn sự kiện đang check-in (Bắt buộc chọn trước khi soát vé) --}}
@php
    $todayEvents = $events->where('time_status', 'today');
    $upcomingEvents = $events->where('time_status', 'upcoming');
    $pastEvents = $events->where('time_status', 'past');
@endphp

<div id="event-selector-card"
    class="mb-6 rounded-2xl border border-primary-200 bg-gradient-to-r from-primary-50/80 via-white to-primary-50/40 p-4 sm:p-5 shadow-xs transition-all relative">

    {{-- Thẻ select ẩn để bảo toàn hoàn toàn tương thích ngược với API / JS --}}
    <select id="event-select" class="hidden">
        <option value="">-- Vui lòng chọn sự kiện cần check-in --</option>
        @foreach ($events as $ev)
            <option value="{{ $ev->id }}"
                data-name="{{ $ev->name }}"
                data-time="{{ $ev->starts_at ? $ev->starts_at->format('H:i, d/m/Y') : 'Chưa định ngày' }}"
                data-location="{{ $ev->location->name ?? 'Chưa có địa điểm' }}"
                data-status="{{ $ev->time_status }}"
                data-checked-in="{{ $ev->checked_in_tickets_count }}"
                data-total="{{ $ev->total_tickets_count }}"
                data-progress="{{ $ev->checkin_progress_pct }}">
                {{ $ev->name }} ({{ $ev->starts_at ? $ev->starts_at->format('d/m/Y') : 'Chưa định ngày' }})
            </option>
        @endforeach
    </select>

    {{-- Phần hiển thị trên giao diện --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        {{-- Tiêu đề và hướng dẫn --}}
        <div class="flex items-start sm:items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-primary-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-primary-600/30">
                <x-icons.calendar class="w-5 h-5" />
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-primary-900">
                        Sự kiện đang soát vé <span class="text-red-500">*</span>
                    </label>
                    @if ($todayEvents->count() > 0)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800 border border-emerald-300">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Hôm nay có {{ $todayEvents->count() }} sự kiện
                        </span>
                    @endif
                </div>
                <p id="event-info-subtext" class="text-xs text-gray-500 mt-0.5">
                    Hệ thống chỉ chấp nhận vé thuộc đúng sự kiện đang chọn
                </p>
            </div>
        </div>

        {{-- Nút Trigger Mở Hộp Chọn Sự Kiện (Custom Combobox) --}}
        <div class="relative w-full lg:w-auto lg:min-w-[480px]">
            {{-- Trạng thái 1: Chưa chọn sự kiện --}}
            <button type="button" id="btn-open-event-picker"
                class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl border border-dashed border-primary-300 bg-white hover:bg-primary-50/50 hover:border-primary-500 text-left transition shadow-xs group cursor-pointer">
                <div class="flex items-center gap-2.5 text-gray-600 group-hover:text-primary-700">
                    <x-icons.search class="w-4 h-4 text-primary-500 shrink-0" />
                    <span class="text-sm font-semibold">Chọn hoặc tìm kiếm sự kiện cần soát vé...</span>
                </div>
                <span class="hidden sm:inline-flex items-center text-xs font-medium text-gray-400 bg-gray-100 px-2 py-0.5 rounded-md border border-gray-200">
                    Nhấn để tìm
                </span>
            </button>

            {{-- Trạng thái 2: Đã chọn sự kiện (Card chi tiết) --}}
            <div id="selected-event-display"
                class="hidden w-full rounded-xl border border-primary-300 bg-white p-3.5 shadow-sm transition hover:border-primary-400">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <h4 id="display-event-name" class="text-sm font-bold text-gray-900 truncate">
                                Tên sự kiện
                            </h4>
                            <span id="display-event-status-badge" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider">
                                --
                            </span>
                        </div>
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-500">
                            <span class="flex items-center gap-1">
                                <x-icons.clock class="w-3.5 h-3.5 text-gray-400 shrink-0" />
                                <span id="display-event-time">--</span>
                            </span>
                            <span class="flex items-center gap-1">
                                <x-icons.location class="w-3.5 h-3.5 text-gray-400 shrink-0" />
                                <span id="display-event-location">--</span>
                            </span>
                        </div>

                        {{-- Thanh tiến độ check-in nhỏ --}}
                        <div class="mt-2.5 flex items-center gap-3">
                            <div class="flex-1 h-1.5 rounded-full bg-gray-100 overflow-hidden">
                                <div id="display-event-progress-bar" class="h-full bg-primary-600 rounded-full transition-all duration-500" style="width: 0%"></div>
                            </div>
                            <span id="display-event-progress-text" class="text-[11px] font-semibold text-gray-600 shrink-0">
                                Đã soát: 0/0 vé (0%)
                            </span>
                        </div>
                    </div>

                    {{-- Nút đổi sự kiện --}}
                    <div class="flex items-center gap-1 shrink-0">
                        <button type="button" id="btn-change-event"
                            class="px-2.5 py-1.5 rounded-lg text-xs font-semibold text-primary-700 bg-primary-50 hover:bg-primary-100 transition flex items-center gap-1 cursor-pointer"
                            title="Đổi sự kiện khác">
                            <x-icons.refresh class="w-3.5 h-3.5" />
                            <span>Đổi</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Dropdown Popover Tìm Kiếm Sự Kiện --}}
            <div id="event-picker-dropdown"
                class="hidden absolute right-0 left-0 top-full mt-2 z-50 rounded-2xl border border-gray-200 bg-white p-3 shadow-2xl transition-all animate-fade-in w-full md:min-w-[500px]">
                
                {{-- Ô tìm kiếm có kính lúp --}}
                <div class="relative mb-2.5">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <x-icons.search class="w-4 h-4" />
                    </div>
                    <input type="text" id="event-search-input"
                        placeholder="Gõ tên sự kiện, ngày hoặc địa điểm..."
                        autocomplete="off"
                        class="w-full pl-9 pr-8 py-2 rounded-xl border border-gray-200 bg-gray-50 text-xs sm:text-sm font-medium text-gray-900 placeholder:text-gray-400 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition">
                    <button type="button" id="btn-clear-search"
                        class="hidden absolute inset-y-0 right-0 pr-2.5 flex items-center text-gray-400 hover:text-gray-600 cursor-pointer">
                        <x-icons.close class="w-4 h-4" />
                    </button>
                </div>

                {{-- Tab lọc nhanh trạng thái --}}
                <div class="flex items-center gap-1.5 pb-2 border-b border-gray-100 text-xs">
                    <button type="button" data-filter="all"
                        class="btn-filter-tab active px-2.5 py-1 rounded-lg font-semibold bg-primary-100 text-primary-800 transition cursor-pointer">
                        Tất cả ({{ $events->count() }})
                    </button>
                    <button type="button" data-filter="today"
                        class="btn-filter-tab px-2.5 py-1 rounded-lg font-semibold text-gray-600 hover:bg-gray-100 transition cursor-pointer flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Hôm nay ({{ $todayEvents->count() }})
                    </button>
                    <button type="button" data-filter="upcoming"
                        class="btn-filter-tab px-2.5 py-1 rounded-lg font-semibold text-gray-600 hover:bg-gray-100 transition cursor-pointer">
                        Sắp tới ({{ $upcomingEvents->count() }})
                    </button>
                </div>

                {{-- Danh sách sự kiện cuộn mượt --}}
                <div id="event-list-container" class="max-h-72 overflow-y-auto divide-y divide-gray-100 pt-1 space-y-1">
                    @forelse($events as $ev)
                        <div class="event-item-row p-2.5 rounded-xl hover:bg-primary-50/70 border border-transparent hover:border-primary-200 transition cursor-pointer group"
                            data-id="{{ $ev->id }}"
                            data-name="{{ $ev->name }}"
                            data-time="{{ $ev->starts_at ? $ev->starts_at->format('H:i, d/m/Y') : 'Chưa định ngày' }}"
                            data-location="{{ $ev->location->name ?? 'Chưa có địa điểm' }}"
                            data-status="{{ $ev->time_status }}"
                            data-checked-in="{{ $ev->checked_in_tickets_count }}"
                            data-total="{{ $ev->total_tickets_count }}"
                            data-progress="{{ $ev->checkin_progress_pct }}">
                            
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-1.5 mb-0.5">
                                        <span class="event-item-name text-xs sm:text-sm font-bold text-gray-900 group-hover:text-primary-700 transition">
                                            {{ $ev->name }}
                                        </span>
                                        @if($ev->time_status === 'today')
                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.2 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                Hôm nay
                                            </span>
                                        @elseif($ev->time_status === 'upcoming')
                                            <span class="inline-flex items-center px-1.5 py-0.2 rounded-md text-[10px] font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                                Sắp tới
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-1.5 py-0.2 rounded-md text-[10px] font-medium bg-gray-100 text-gray-600">
                                                Đã qua
                                            </span>
                                        @endif
                                    </div>

                                    <div class="flex flex-wrap items-center gap-x-3 gap-y-0.5 text-[11px] text-gray-500">
                                        <span class="flex items-center gap-1">
                                            <x-icons.clock class="w-3 h-3 text-gray-400" />
                                            <span>{{ $ev->starts_at ? $ev->starts_at->format('H:i - d/m/Y') : 'Chưa định ngày' }}</span>
                                        </span>
                                        @if($ev->location)
                                            <span class="flex items-center gap-1 truncate max-w-[200px]">
                                                <x-icons.location class="w-3 h-3 text-gray-400" />
                                                <span>{{ $ev->location->name }}</span>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Tiến độ soát vé --}}
                                <div class="text-right shrink-0">
                                    <span class="text-[11px] font-bold text-gray-700 block">
                                        {{ $ev->checked_in_tickets_count }}/{{ $ev->total_tickets_count }} vé
                                    </span>
                                    <span class="text-[10px] font-medium text-primary-600">
                                        {{ $ev->checkin_progress_pct }}%
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-6 text-center text-xs text-gray-500">
                            Không có sự kiện nào trong hệ thống.
                        </div>
                    @endforelse
                </div>

                {{-- Thông báo không tìm thấy kết quả --}}
                <div id="event-search-empty" class="hidden py-6 text-center text-xs text-gray-500">
                    <x-icons.search class="w-6 h-6 text-gray-300 mx-auto mb-1.5" />
                    <p class="font-semibold text-gray-700">Không tìm thấy sự kiện phù hợp</p>
                    <p class="text-[11px] text-gray-400 mt-0.5">Thử tìm bằng từ khóa khác hoặc xóa bộ lọc</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Cảnh báo khi chưa chọn sự kiện --}}
    <div id="event-warning-hint" class="hidden mt-3 pt-3 border-t border-red-200 text-xs text-red-600 font-semibold flex items-center gap-1.5 animate-pulse">
        <x-icons.exclamation class="w-4 h-4 shrink-0" />
        <span>Vui lòng chọn sự kiện bạn đang thực hiện check-in trước khi quét vé!</span>
    </div>
</div>
