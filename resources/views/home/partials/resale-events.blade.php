@props(['events'])

{{-- Mục "Bán lại vé": Div ảnh mascot nằm cùng container cuộn, ẩn/hiện nút trái/phải theo đúng vị trí [0] và [n-1] --}}
@if($events->isNotEmpty())
    <section class="relative py-4">
        {{-- Tiêu đề section: Icon 2 tấm vé + Bán lại vé > --}}
        <div class="mb-4">
            <a href="{{ route('events.index', 've-ban-lai') }}"
                class="group inline-flex items-center gap-2 text-xl sm:text-2xl font-extrabold text-white hover:text-emerald-400 transition-colors">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-500 text-black p-1">
                    <svg class="h-5 w-5 fill-current" viewBox="0 0 24 24">
                        <path
                            d="M4 4h16a2 2 0 0 1 2 2v3a2 2 0 0 0 0 4v3a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-3a2 2 0 0 0 0-4V6a2 2 0 0 1 2-2zm0 2v1.5a3.5 3.5 0 0 1 0 7V16h16v-1.5a3.5 3.5 0 0 1 0-7V6H4z" />
                    </svg>
                </span>
                <span>Bán lại vé</span>
                <svg class="h-5 w-5 text-gray-400 group-hover:text-emerald-400 group-hover:translate-x-1 transition-all"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>

        {{-- Khung bao bọc chứa dải sóng xanh lượn phía chân --}}
        <div class="relative overflow-hidden rounded-3xl pt-2 pb-6 group">
            {{-- Dải ảnh nền SVG sóng xanh lượn ở phần chân (bg-ve-ban-lai.svg) được tăng độ cao để mascot đứng lên nền xanh --}}
            <div class="pointer-events-none absolute bottom-0 left-0 right-0 h-[280px] sm:h-[320px] md:h-[350px] z-0">
                <img src="{{ asset('storage/images/bg-ve-ban-lai.svg') }}" alt="Dải sóng vé bán lại"
                    class="h-full w-full object-fill object-bottom">
            </div>

            {{-- Nút cuộn sang trái: Ẩn khi ở đầu [0], hiện khi đã cuộn sang phải --}}
            <button type="button" id="resale-scroll-left"
                class="hidden absolute left-0 top-1/2 -translate-y-1/2 h-12 w-8 items-center justify-center rounded-md bg-black/90 text-white border border-white/20 shadow-2xl transition duration-300 hover:bg-emerald-500 hover:border-emerald-500 z-20 cursor-pointer"
                aria-label="Cuộn vé bán lại sang trái">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            {{-- Container cuộn ngang: Chứa cả div ảnh mascot lẫn các thẻ vé bán lại --}}
            <div id="resale-scroll-container"
                class="relative z-10 flex items-stretch gap-4 sm:gap-6 overflow-x-auto pb-4 pt-1 scroll-smooth no-scrollbar [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">

                {{-- Div 1: Ảnh logo Resale ticket 3D & Mascot đứng trên nền sóng xanh --}}
                <div class="shrink-0 flex items-end justify-center w-[200px] sm:w-[260px] md:w-[290px] pb-1">
                    <a href="{{ route('events.index', 've-ban-lai') }}"
                        class="block w-full">
                        <img src="{{ asset('storage/images/resale-badge-mascot.png') }}" alt="Vé bán lại - Resale Ticket"
                            class="w-full h-auto max-h-[250px] sm:max-h-[280px] md:max-h-[300px] object-contain drop-shadow-2xl">
                    </a>
                </div>

                {{-- Các thẻ vé bán lại (Kích thước width rộng hơn) --}}
                @foreach($events as $event)
                    <a href="{{ route('events.show', $event->slug) }}"
                        class="shrink-0 w-[270px] sm:w-[330px] md:w-[370px] rounded-2xl bg-[#1b221e]/90 backdrop-blur-md border border-white/10 p-3.5 flex flex-col justify-between transition-colors duration-300 hover:border-emerald-400/50 hover:shadow-2xl group/card">
                        {{-- Ảnh sự kiện tỉ lệ rộng đẹp mắt --}}
                        <div class="relative aspect-[16/10] w-full overflow-hidden rounded-xl bg-gray-800 mb-3">
                            @if($event->image)
                                <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->name }}"
                                    class="h-full w-full object-cover">
                            @else
                                <div class="flex h-full w-full items-center justify-center bg-gray-800 text-xs text-gray-500">
                                    {{ $event->name }}
                                </div>
                            @endif
                        </div>

                        {{-- Thông tin vé bán lại (Tên sự kiện + Địa điểm & Ngày) --}}
                        <div class="flex flex-col flex-1 justify-between">
                            <h3
                                class="line-clamp-2 text-sm sm:text-base font-bold text-white group-hover/card:text-emerald-300 transition-colors leading-snug">
                                {{ $event->name }}
                            </h3>

                            <div class="mt-3 flex items-center text-xs sm:text-sm text-gray-300">
                                <svg class="mr-1 h-3.5 w-3.5 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="truncate">
                                    {{ $event->location ? $event->location->city : 'Toàn quốc' }}
                                </span>
                                <span class="mx-1.5 text-gray-400">•</span>
                                <span class="shrink-0 font-medium">
                                    {{ $event->starts_at ? $event->starts_at->format('d \T\h m') : 'Đang cập nhật' }}
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Nút cuộn sang phải: Hiện khi ở đầu [0], ẩn khi chạm cuối [n-1] --}}
            <button type="button" id="resale-scroll-right"
                class="flex absolute right-0 top-1/2 -translate-y-1/2 h-12 w-8 items-center justify-center rounded-md bg-black/90 text-white border border-white/20 shadow-2xl transition duration-300 hover:bg-emerald-500 hover:border-emerald-500 z-20 cursor-pointer"
                aria-label="Cuộn vé bán lại sang phải">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>
    </section>

    {{-- Script điều khiển ẩn/hiện nút cuộn trái/phải theo đúng vị trí [0] và [n-1] --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('resale-scroll-container');
            const btnLeft = document.getElementById('resale-scroll-left');
            const btnRight = document.getElementById('resale-scroll-right');

            function updateResaleButtons() {
                if (!container || !btnLeft || !btnRight) return;

                const scrollLeft = container.scrollLeft;
                const maxScrollLeft = container.scrollWidth - container.clientWidth;

                // 1. Ở vị trí đầu [0]: Ẩn nút trái, Hiện nút phải
                if (scrollLeft <= 5) {
                    btnLeft.classList.add('hidden');
                    btnLeft.classList.remove('flex');

                    btnRight.classList.remove('hidden');
                    btnRight.classList.add('flex');
                }
                // 2. Ở vị trí cuối [n-1]: Hiện nút trái, Ẩn nút phải
                else if (scrollLeft >= maxScrollLeft - 5) {
                    btnLeft.classList.remove('hidden');
                    btnLeft.classList.add('flex');

                    btnRight.classList.add('hidden');
                    btnRight.classList.remove('flex');
                }
                // 3. Ở giữa: Hiện cả 2 nút
                else {
                    btnLeft.classList.remove('hidden');
                    btnLeft.classList.add('flex');

                    btnRight.classList.remove('hidden');
                    btnRight.classList.add('flex');
                }
            }

            if (container) {
                // Lắng nghe sự kiện cuộn
                container.addEventListener('scroll', updateResaleButtons, { passive: true });

                // Click cuộn sang phải
                if (btnRight) {
                    btnRight.addEventListener('click', function () {
                        container.scrollBy({ left: 380, behavior: 'smooth' });
                        setTimeout(updateResaleButtons, 350);
                    });
                }

                // Click cuộn sang trái
                if (btnLeft) {
                    btnLeft.addEventListener('click', function () {
                        container.scrollBy({ left: -380, behavior: 'smooth' });
                        setTimeout(updateResaleButtons, 350);
                    });
                }

                // Khởi tạo trạng thái ban đầu
                updateResaleButtons();
            }
        });
    </script>
@endif