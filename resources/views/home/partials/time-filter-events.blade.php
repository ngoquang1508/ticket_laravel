@props(['weekendEvents', 'monthEvents'])

{{-- Sự kiện lọc theo Thời gian: Cuối tuần này & Tháng này (Ảnh 2) --}}
<section class="py-4">
    {{-- Thanh chọn Tab thời gian --}}
    <div class="mb-6 flex items-center gap-3">
        <button type="button" id="tab-btn-weekend"
            class="time-tab-btn rounded-full px-4 py-1.5 sm:px-5 sm:py-2 text-xs sm:text-sm font-semibold transition-all duration-200 bg-emerald-500 text-white shadow-lg shadow-emerald-500/20"
            data-target="weekend-panel">
            Cuối tuần này
        </button>
        <button type="button" id="tab-btn-month"
            class="time-tab-btn rounded-full px-4 py-1.5 sm:px-5 sm:py-2 text-xs sm:text-sm font-semibold transition-all duration-200 border border-white/20 text-gray-300 hover:border-white/40 hover:text-white"
            data-target="month-panel">
            Tháng này
        </button>
    </div>

    {{-- Panel Cuối tuần này --}}
    <div id="weekend-panel" class="time-panel transition-opacity duration-300">
        @if($weekendEvents->isNotEmpty())
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                @foreach($weekendEvents as $event)
                    @include('home.partials.event-card-compact', ['event' => $event])
                @endforeach
            </div>
        @else
            <div class="rounded-xl border border-white/10 bg-[#1c1c1c] p-8 text-center text-gray-400">
                Chưa có sự kiện nào diễn ra vào cuối tuần này.
            </div>
        @endif
    </div>

    {{-- Panel Tháng này (Ẩn mặc định) --}}
    <div id="month-panel" class="time-panel hidden transition-opacity duration-300">
        @if($monthEvents->isNotEmpty())
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                @foreach($monthEvents as $event)
                    @include('home.partials.event-card-compact', ['event' => $event])
                @endforeach
            </div>
        @else
            <div class="rounded-xl border border-white/10 bg-[#1c1c1c] p-8 text-center text-gray-400">
                Chưa có sự kiện nào diễn ra trong tháng này.
            </div>
        @endif
    </div>
</section>

{{-- Script chuyển Tab thời gian nhanh chóng --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tabs = document.querySelectorAll('.time-tab-btn');
        const panels = document.querySelectorAll('.time-panel');

        tabs.forEach(tab => {
            tab.addEventListener('click', function () {
                const targetId = this.getAttribute('data-target');

                // Đổi trạng thái nút
                tabs.forEach(t => {
                    t.classList.remove('bg-emerald-500', 'text-white', 'shadow-lg', 'shadow-emerald-500/20');
                    t.classList.add('border', 'border-white/20', 'text-gray-300');
                });
                this.classList.add('bg-emerald-500', 'text-white', 'shadow-lg', 'shadow-emerald-500/20');
                this.classList.remove('border', 'border-white/20', 'text-gray-300');

                // Hiển thị panel tương ứng
                panels.forEach(panel => {
                    if (panel.id === targetId) {
                        panel.classList.remove('hidden');
                    } else {
                        panel.classList.add('hidden');
                    }
                });
            });
        });
    });
</script>