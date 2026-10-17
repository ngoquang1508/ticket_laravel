@props(['events'])

{{-- Sự kiện xu hướng (10 sự kiện với số thứ tự 3D 1-10, ẩn/hiện nút cuộn trái phải theo vị trí cuộn) --}}
@if($events->isNotEmpty())
    <section class="py-4">
        {{-- Tiêu đề section --}}
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg sm:text-xl font-bold text-white flex items-center gap-2">
                <span>🔥</span>
                <span>Sự kiện xu hướng</span>
            </h2>
        </div>

        {{-- Danh sách cuộn ngang 10 sự kiện kèm nút trượt trái và phải theo logic --}}
        <div class="relative group">
            {{-- Nút cuộn sang trái: Ẩn khi ở đầu [0], hiện khi đã cuộn sang phải --}}
            <button type="button" id="trending-scroll-left"
                class="hidden absolute -left-2 sm:-left-4 top-[40%] -translate-y-1/2 h-10 w-10 items-center justify-center rounded-full bg-black/85 text-white border border-white/20 shadow-2xl backdrop-blur-md transition duration-300 hover:bg-emerald-500 z-20 cursor-pointer"
                aria-label="Cuộn sang trái">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            {{-- Container cuộn ngang chứa đúng 10 sự kiện --}}
            <div id="trending-scroll-container"
                class="flex gap-4 sm:gap-6 overflow-x-auto pb-4 pt-2 scroll-smooth no-scrollbar [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">
                @foreach($events as $index => $event)
                    @php
                        // Fallback overlap 15% tương ứng với từng số
                        $defaultOverlap = match($index + 1) {
                            1 => '-ml-[4px] sm:-ml-[5px]',
                            10 => '-ml-[8px] sm:-ml-[10px]',
                            default => '-ml-[5px] sm:-ml-[7px]',
                        };
                    @endphp
                    <div class="trending-item flex-shrink-0 flex items-end">
                        {{-- Số thứ tự: width co giãn tự nhiên theo nội dung số --}}
                        <div class="trending-number shrink-0 flex items-end select-none pointer-events-none pb-0.5">
                            <span class="text-6xl sm:text-7xl font-black tracking-tighter leading-none select-none text-transparent bg-clip-text bg-gradient-to-b from-emerald-100 via-emerald-400 to-emerald-700 drop-shadow-[2px_3px_0px_#064e3b] drop-shadow-[0_4px_8px_rgba(0,0,0,0.8)] block">
                                {{ $index + 1 }}
                            </span>
                        </div>

                        {{-- Thẻ sự kiện: đè lên 15% của số, không hover/scale --}}
                        <div class="trending-card {{ $defaultOverlap }} w-[210px] sm:w-[250px]">
                            <a href="{{ route('events.show', $event->slug) }}"
                                class="block relative aspect-[16/9] w-full overflow-hidden rounded-xl bg-gray-800">
                                @if($event->image)
                                    <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->name }}"
                                        class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full w-full items-center justify-center bg-gray-800 p-2 text-center text-xs text-gray-400">
                                        {{ $event->name }}
                                    </div>
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-transparent to-transparent pointer-events-none"></div>
                                <div class="absolute bottom-2 left-2 right-2 text-xs font-medium text-white truncate drop-shadow">
                                    {{ $event->name }}
                                </div>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Nút cuộn sang phải: Hiện khi ở đầu [0], ẩn khi cuộn đến cuối [n-1] --}}
            <button type="button" id="trending-scroll-right"
                class="flex absolute -right-2 sm:-right-4 top-[40%] -translate-y-1/2 h-10 w-10 items-center justify-center rounded-full bg-black/85 text-white border border-white/20 shadow-2xl backdrop-blur-md transition duration-300 hover:bg-emerald-500 z-20 cursor-pointer"
                aria-label="Cuộn sang phải">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>
    </section>

    {{-- Script điều khiển ẩn/hiện nút cuộn trái/phải theo đúng logic: --}}
    {{-- Ở vị trí [0]: Ẩn nút trái, Hiện nút phải --}}
    {{-- Ở giữa: Hiện nút trái, Hiện nút phải --}}
    {{-- Ở vị trí [n-1] (cuối cùng): Hiện nút trái, Ẩn nút phải --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('trending-scroll-container');
            const btnLeft = document.getElementById('trending-scroll-left');
            const btnRight = document.getElementById('trending-scroll-right');

            function updateScrollButtons() {
                if (!container || !btnLeft || !btnRight) return;

                const scrollLeft = container.scrollLeft;
                const maxScrollLeft = container.scrollWidth - container.clientWidth;

                // 1. Ở vị trí đầu [0]
                if (scrollLeft <= 5) {
                    btnLeft.classList.add('hidden');
                    btnLeft.classList.remove('flex');

                    btnRight.classList.remove('hidden');
                    btnRight.classList.add('flex');
                }
                // 2. Ở vị trí cuối cùng [n-1]
                else if (scrollLeft >= maxScrollLeft - 5) {
                    btnLeft.classList.remove('hidden');
                    btnLeft.classList.add('flex');

                    btnRight.classList.add('hidden');
                    btnRight.classList.remove('flex');
                }
                // 3. Ở giữa (0 < currentIndex < n-1)
                else {
                    btnLeft.classList.remove('hidden');
                    btnLeft.classList.add('flex');

                    btnRight.classList.remove('hidden');
                    btnRight.classList.add('flex');
                }
            }

            if (container) {
                // Lắng nghe sự kiện cuộn
                container.addEventListener('scroll', updateScrollButtons, { passive: true });

                // Click nút cuộn phải
                if (btnRight) {
                    btnRight.addEventListener('click', function () {
                        container.scrollBy({ left: 340, behavior: 'smooth' });
                        setTimeout(updateScrollButtons, 350);
                    });
                }

                // Click nút cuộn trái
                if (btnLeft) {
                    btnLeft.addEventListener('click', function () {
                        container.scrollBy({ left: -340, behavior: 'smooth' });
                        setTimeout(updateScrollButtons, 350);
                    });
                }

                // Kiểm tra trạng thái ban đầu
                updateScrollButtons();
            }

            // Tự động tính toán độ đè bằng chính xác 15% width thực tế của từng số
            function applyTrendingOverlap() {
                document.querySelectorAll('.trending-item').forEach(function(item) {
                    const num = item.querySelector('.trending-number');
                    const card = item.querySelector('.trending-card');
                    if (num && card) {
                        const overlap = num.offsetWidth * 0.15;
                        card.style.marginLeft = `-${overlap.toFixed(1)}px`;
                    }
                });
            }

            applyTrendingOverlap();
            window.addEventListener('resize', applyTrendingOverlap);
        });
    </script>
@endif
