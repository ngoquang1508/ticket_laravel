@props(['events'])

{{-- Mục "Bán lại vé": Div ảnh mascot nằm cùng container cuộn, ẩn/hiện nút trái/phải theo đúng vị trí [0] và [n-1] --}}
@if($events->isNotEmpty())
    <section class="relative py-4">
        {{-- Tiêu đề section: Icon 2 tấm vé + Bán lại vé > --}}
        <div class="mb-4">
            <a href="{{ route('events.index', 've-ban-lai') }}"
                class="group inline-flex items-center gap-2 text-lg sm:text-xl font-bold text-white hover:text-emerald-400 transition-colors">
                <span>Bán lại vé</span>
                <x-icons.chevron-right class="h-5 w-5 text-gray-400 group-hover:text-emerald-400 group-hover:translate-x-1 transition-all" />
            </a>
        </div>

        {{-- Khung bao bọc chứa dải sóng xanh lượn phía chân --}}
        <div class="relative overflow-hidden rounded-3xl pt-2 pb-6 group">
            {{-- Dải ảnh nền SVG sóng xanh lượn ở phần chân (bg-ve-ban-lai.svg) được tăng độ cao để mascot đứng lên nền xanh
            --}}
            <div class="pointer-events-none absolute bottom-0 left-0 right-0 h-[280px] sm:h-[320px] md:h-[350px] z-0">
                <img src="{{ asset('assets/images/bg-ve-ban-lai.svg') }}" alt="Dải sóng vé bán lại"
                    class="h-full w-full object-fill object-bottom">
            </div>

            {{-- Nút cuộn sang trái: Ẩn khi ở đầu [0], hiện khi đã cuộn sang phải --}}
            <button type="button" id="resale-scroll-left"
                class="hidden absolute left-0 top-1/2 -translate-y-1/2 h-10 w-8 items-center justify-center rounded-r-lg bg-black/90 text-white border border-white/20 shadow-2xl transition duration-300 hover:bg-emerald-500 hover:border-emerald-500 z-20 cursor-pointer"
                aria-label="Cuộn vé bán lại sang trái">
                <x-icons.chevron-left class="h-5 w-5" />
            </button>

            {{-- Container cuộn ngang: Chứa cả div ảnh mascot lẫn các thẻ vé bán lại --}}
            <div id="resale-scroll-container"
                class="relative z-10 flex items-stretch gap-4 sm:gap-5 overflow-x-auto pb-4 pt-1 scroll-smooth no-scrollbar [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">

                {{-- Div 1: Ảnh logo Resale ticket 3D & Mascot đứng trên nền sóng xanh --}}
                <div class="shrink-0 flex items-end justify-center w-[170px] sm:w-[210px] md:w-[230px] pb-1">
                    <a href="{{ route('events.index', 've-ban-lai') }}" class="block w-full">
                        <img src="{{ asset('assets/images/resale-badge-mascot.png') }}" alt="Vé bán lại - Resale Ticket"
                            class="w-full h-auto max-h-[220px] sm:max-h-[250px] md:max-h-[270px] object-contain drop-shadow-2xl">
                    </a>
                </div>

                {{-- Các thẻ vé bán lại (Kích thước cân đối, tỷ lệ 16:9) --}}
                @foreach($events as $event)
                    <a href="{{ route('events.show', $event->slug) }}"
                        class="shrink-0 w-[220px] sm:w-[250px] md:w-[270px] rounded-xl bg-[#1b221e]/90 backdrop-blur-md border border-white/10 p-3 flex flex-col justify-between transition-colors duration-300 hover:border-emerald-400/50 hover:shadow-2xl group/card">
                        {{-- Ảnh sự kiện tỉ lệ chuẩn 16:9 --}}
                        <div class="relative aspect-video w-full overflow-hidden rounded-lg bg-gray-800 mb-2.5">
                            @if($event->image)
                                <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->name }}"
                                    class="h-full w-full object-cover">
                            @else
                                <div class="flex h-full w-full items-center justify-center bg-gray-800 text-xs text-gray-500">
                                    {{ $event->name }}
                                </div>
                            @endif
                        </div>

                        {{-- Thông tin vé bán lại (Tên sự kiện + Giá vé + Địa điểm & Ngày) --}}
                        <div class="flex flex-col flex-1 justify-between">
                            <h3
                                class="h-[2.6rem] line-clamp-2 text-sm font-semibold text-white group-hover/card:text-emerald-300 transition-colors leading-snug">
                                {{ $event->name }}
                            </h3>

                            <div class="mt-1 text-xs sm:text-sm font-bold text-emerald-400">
                                @if(isset($event->ticket_types_min_price) && $event->ticket_types_min_price > 0)
                                    Từ {{ number_format($event->ticket_types_min_price, 0, ',', '.') }}đ
                                @else
                                    Từ 0đ
                                @endif
                            </div>

                            <div class="mt-1 flex items-center text-xs text-gray-400">
                                <x-icons.location class="mr-1 h-3.5 w-3.5 shrink-0 text-gray-400" />
                                <span class="truncate">
                                    {{ $event->location ? $event->location->city : 'Toàn quốc' }}
                                </span>
                                <span class="mx-1 text-gray-500">•</span>
                                <span class="shrink-0 font-medium">
                                    {{ $event->starts_at ? $event->starts_at->format('d/m') : 'Đang cập nhật' }}
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
                <x-icons.chevron-right class="h-5 w-5" />
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