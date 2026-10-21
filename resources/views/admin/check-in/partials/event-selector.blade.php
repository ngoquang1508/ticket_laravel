{{-- Khối Chọn sự kiện đang check-in (Bắt buộc chọn trước khi soát vé) --}}
<div id="event-selector-card"
    class="mb-6 rounded-2xl border border-primary-200 bg-gradient-to-r from-primary-50/80 via-white to-primary-50/40 p-4 sm:p-5 shadow-xs transition-all">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-primary-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-primary-600/30">
                <x-icons.calendar class="w-5 h-5" />
            </div>
            <div>
                <label for="event-select" class="block text-xs font-bold uppercase tracking-wider text-primary-900">
                    Sự kiện đang soát vé <span class="text-red-500">*</span>
                </label>
                <p id="event-info-subtext" class="text-xs text-gray-500 mt-0.5">Hệ thống chỉ chấp nhận vé thuộc đúng sự kiện đang chọn</p>
            </div>
        </div>

        {{-- Dropdown danh sách sự kiện --}}
        <div class="w-full md:w-auto md:min-w-[380px] lg:min-w-[460px]">
            <select id="event-select"
                class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-900 shadow-xs focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition cursor-pointer">
                <option value="">-- Vui lòng chọn sự kiện cần check-in --</option>
                @foreach ($events as $ev)
                    <option value="{{ $ev->id }}"
                        data-name="{{ $ev->name }}"
                        data-time="{{ $ev->starts_at ? $ev->starts_at->format('H:i, d/m/Y') : '' }}"
                        data-location="{{ $ev->location->name ?? '' }}">
                        {{ $ev->name }} ({{ $ev->starts_at ? $ev->starts_at->format('d/m/Y') : 'Chưa định ngày' }})
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Cảnh báo khi chưa chọn sự kiện --}}
    <div id="event-warning-hint" class="hidden mt-3 pt-3 border-t border-red-200 text-xs text-red-600 font-semibold flex items-center gap-1.5 animate-pulse">
        <x-icons.exclamation class="w-4 h-4 shrink-0" />
        <span>Vui lòng chọn sự kiện bạn đang thực hiện check-in trước khi quét vé!</span>
    </div>
</div>
