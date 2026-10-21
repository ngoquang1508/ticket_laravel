{{--
Component Popover Bộ Lọc Tổng Hợp: Vị trí, Giá tiền, Thể loại (Ticketbox Style)
--}}

<div id="popover-main-filter" style="color-scheme: light;"
    class="hidden fixed top-24 left-3 right-3 sm:left-auto sm:right-0 sm:top-full sm:absolute sm:mt-2 z-50 bg-white text-gray-800 rounded-2xl shadow-2xl border border-gray-100 p-4 sm:p-5 w-auto sm:w-[360px] max-w-[calc(100vw-1.5rem)] sm:max-w-none max-h-[85vh] overflow-y-auto">

    {{-- Tiêu đề và nút Đóng trên Mobile --}}
    <div class="flex items-center justify-between pb-3 mb-3 border-b border-gray-100 sm:hidden">
        <span class="font-bold text-gray-900 text-sm">Bộ lọc</span>
        <button type="button" class="btn-close-popover p-1 text-gray-400 hover:text-gray-600 cursor-pointer">
            <x-icons.close class="w-5 h-5" />
        </button>
    </div>

    {{-- Khối 1: Vị trí (Location) với Custom Radio nền trắng chuẩn Ticketbox --}}
    <div>
        <h3 class="text-sm font-bold text-gray-900 mb-2.5">Vị trí</h3>
        <div class="space-y-1 text-xs sm:text-sm">
            @php
                $currentLoc = $locationFilter ?? 'all';
                $locationsList = [
                    'all' => 'Toàn quốc',
                    'hn' => 'Hà Nội',
                    'hcm' => 'Hồ Chí Minh',
                    'danang' => 'Đà Nẵng',
                    'other' => 'Vị trí khác',
                ];
            @endphp
            @foreach($locationsList as $locVal => $locLabel)
                <label
                    class="flex items-center gap-3 cursor-pointer select-none py-1 px-1 rounded-lg hover:bg-gray-50 transition">
                    <input type="radio" name="temp_location" value="{{ $locVal }}" class="sr-only peer" {{ $currentLoc === $locVal ? 'checked' : '' }}>
                    <span
                        class="w-4 h-4 rounded-full border-2 border-gray-300 bg-white p-[2px] flex items-center justify-center peer-checked:border-emerald-500 peer-checked:[&>span]:scale-100 transition-all shrink-0">
                        <span class="w-full h-full rounded-full bg-emerald-500 scale-0 transition-transform"></span>
                    </span>
                    <span
                        class="text-gray-700 text-xs sm:text-sm peer-checked:text-emerald-600 peer-checked:font-bold transition-colors">{{ $locLabel }}</span>
                </label>
            @endforeach
        </div>
    </div>

    {{-- Phân cách nét đứt --}}
    <div class="border-b border-dashed border-gray-200 my-4"></div>

    {{-- Khối 2: Giá tiền (Toggle Switch Miễn phí) --}}
    <div>
        <h3 class="text-sm font-bold text-gray-900 mb-2.5">Giá tiền</h3>
        <div class="flex items-center justify-between">
            <span class="text-xs sm:text-sm text-gray-700 font-medium">Miễn phí</span>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" id="temp-toggle-free" class="sr-only peer" {{ $isFree ? 'checked' : '' }}>
                <div
                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500">
                </div>
            </label>
        </div>
    </div>

    {{-- Phân cách nét đứt --}}
    <div class="border-b border-dashed border-gray-200 my-4"></div>

    {{-- Khối 3: Thể loại (Pill tags) --}}
    <div>
        <h3 class="text-sm font-bold text-gray-900 mb-2.5">Thể loại</h3>
        <div class="flex flex-wrap gap-2 max-h-40 overflow-y-auto pr-1">
            @php
                $activeCategorySlugs = !empty($selectedCategories) ? $selectedCategories : (isset($currentCategory) ? [$currentCategory->slug] : []);
            @endphp
            @foreach($allCategories as $cat)
                @php
                    $isCatChecked = in_array($cat->slug, $activeCategorySlugs);
                @endphp
                <button type="button"
                    class="btn-category-pill px-2 py-1 rounded-full text-[12px] font-medium border transition cursor-pointer select-none {{ $isCatChecked ? 'border-emerald-500 bg-emerald-50 text-emerald-600 font-bold shadow-sm' : 'border-gray-200 text-gray-700 bg-gray-50/80 hover:border-gray-300' }}"
                    data-slug="{{ $cat->slug }}">
                    {{ $cat->name }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- Phân cách nét đứt --}}
    <div class="border-b border-dashed border-gray-200 my-4"></div>

    {{-- Nút Chân Popover Bộ Lọc --}}
    <div class="flex items-center justify-between gap-3">
        <button type="button" id="btn-reset-main-filter"
            class="w-1/2 py-2.5 rounded-xl border border-emerald-500 text-emerald-600 hover:bg-emerald-50 font-semibold text-xs sm:text-sm transition cursor-pointer text-center">
            Thiết lập lại
        </button>
        <button type="button" id="btn-apply-main-filter"
            class="w-1/2 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs sm:text-sm shadow-md shadow-emerald-500/20 transition cursor-pointer text-center">
            Áp dụng
        </button>
    </div>
</div>