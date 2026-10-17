@props(['events'])

{{-- Hero Banner Slider: Hiển thị 2 ảnh song song với infinite seamless loop (vòng lặp vô hạn liền mạch) --}}
@if($events->isNotEmpty())
    @php
        $n = $events->count(); // 3 sự kiện
        // Clone 2 item ở đầu và 2 item ở cuối để trượt vô hạn cả 2 chiều mà không bao giờ bị giật lùi
        $cloneBefore = collect([$events[1], $events[2]]);
        $cloneAfter = collect([$events[0], $events[1]]);
        $displayEvents = $cloneBefore->concat($events)->concat($cloneAfter);
    @endphp

    <section class="relative pt-6 overflow-hidden" id="hero-slider-section">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="relative group" id="hero-slider-wrapper">
                {{-- Khung nhìn slider --}}
                <div class="overflow-hidden rounded-2xl" id="hero-viewport">
                    {{-- Dải track chứa các card --}}
                    <div class="flex gap-4 will-change-transform" id="hero-track">
                        @foreach($displayEvents as $index => $event)
                            <div class="hero-card-item shrink-0 w-full md:w-[calc(50%-8px)]">
                                <div class="relative aspect-[16/9] w-full overflow-hidden rounded-2xl bg-gray-900 border border-white/10 shadow-2xl group/card">
                                    {{-- Ảnh sự kiện --}}
                                    @if($event->image)
                                        <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->name }}"
                                            class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center bg-gray-900 text-lg font-bold text-gray-500">
                                            {{ $event->name }}
                                        </div>
                                    @endif

                                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent pointer-events-none"></div>

                                    {{-- Nút "Xem chi tiết" nền trắng chữ đen góc dưới bên trái --}}
                                    <div class="absolute bottom-3 left-3 z-10">
                                        <a href="{{ route('events.show', $event->slug) }}"
                                            class="inline-flex items-center justify-center rounded-md bg-white px-3 py-1.5 text-xs font-bold text-gray-900 shadow-md transition hover:bg-gray-200">
                                            Xem chi tiết
                                        </a>
                                    </div>

                                    {{-- Nút play video góc dưới bên phải --}}
                                    <div class="absolute bottom-3 right-3 z-10">
                                        <a href="{{ route('events.show', $event->slug) }}"
                                            class="flex h-7 w-7 items-center justify-center rounded-md bg-black/60 text-white backdrop-blur-sm transition hover:bg-black/90"
                                            aria-label="Xem chi tiết sự kiện">
                                            <svg class="h-3.5 w-3.5 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Nút cuộn trái: Luôn luôn chuyển động từ trái sang phải --}}
                <button type="button" id="hero-prev"
                    class="flex absolute left-3 top-1/2 -translate-y-1/2 h-8 w-8 items-center justify-center rounded-full bg-black/60 hover:bg-black/90 text-white/90 backdrop-blur-sm transition z-20 shadow-lg cursor-pointer"
                    aria-label="Slide trước">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                {{-- Nút cuộn phải: Luôn luôn chuyển động từ phải sang trái --}}
                <button type="button" id="hero-next"
                    class="flex absolute right-3 top-1/2 -translate-y-1/2 h-8 w-8 items-center justify-center rounded-full bg-black/60 hover:bg-black/90 text-white/90 backdrop-blur-sm transition z-20 shadow-lg cursor-pointer"
                    aria-label="Slide tiếp theo">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                {{-- 3 Dots phân trang ở dưới như ảnh 1 --}}
                <div class="mt-3 flex justify-center gap-1.5" id="hero-dots">
                    @for($i = 0; $i < $n; $i++)
                        <button type="button" data-index="{{ $i }}"
                            class="h-1 rounded-full transition-all duration-300 {{ $i === 0 ? 'w-6 bg-emerald-500' : 'w-2 bg-white/30' }}"
                            aria-label="Đến ảnh {{ $i + 1 }}"></button>
                    @endfor
                </div>
            </div>
        </div>
    </section>

    {{-- Script Infinite Seamless Loop: Clone 2 đầu, reset vị trí ngầm không animation sau khi đi qua clone --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const track = document.getElementById('hero-track');
            const btnPrev = document.getElementById('hero-prev');
            const btnNext = document.getElementById('hero-next');
            const dots = document.querySelectorAll('#hero-dots button');
            const n = {{ $n }}; // 3 sự kiện
            const START_INDEX = 2; // Vị trí của Event 0 thật trong mảng displayEvents
            let currentIndex = START_INDEX;
            let isTransitioning = false;
            let autoTimer = null;

            // Tính toán khoảng cách dịch chuyển cho mỗi card (bao gồm gap 16px)
            function getStepWidth() {
                const firstCard = track.children[0];
                return firstCard ? firstCard.offsetWidth + 16 : 0;
            }

            // Cập nhật trạng thái hiển thị của các dots
            function updateDots() {
                let realIndex = (currentIndex - START_INDEX) % n;
                if (realIndex < 0) realIndex += n;

                dots.forEach((dot, i) => {
                    if (i === realIndex) {
                        dot.classList.remove('w-2', 'bg-white/30');
                        dot.classList.add('w-6', 'bg-emerald-500');
                    } else {
                        dot.classList.remove('w-6', 'bg-emerald-500');
                        dot.classList.add('w-2', 'bg-white/30');
                    }
                });
            }

            // Di chuyển đến index với hiệu ứng chuyển động mượt
            function moveToIndex(index) {
                if (isTransitioning) return;
                isTransitioning = true;
                currentIndex = index;
                const stepWidth = getStepWidth();
                track.style.transition = 'transform 0.5s ease-out';
                track.style.transform = `translateX(-${currentIndex * stepWidth}px)`;
                updateDots();
            }

            // Khi animation trượt hoàn tất: nếu chạm clone thì lập tức reset vị trí ngầm KHÔNG có animation
            track.addEventListener('transitionend', function () {
                isTransitioning = false;
                const stepWidth = getStepWidth();

                // Nếu vừa trượt qua clone phía sau (Next qua item cuối về đầu)
                if (currentIndex >= START_INDEX + n) {
                    track.style.transition = 'none';
                    currentIndex = currentIndex - n;
                    track.style.transform = `translateX(-${currentIndex * stepWidth}px)`;
                    void track.offsetWidth; // Ép browser reflow
                }
                // Nếu vừa trượt qua clone phía trước (Prev qua item đầu về cuối)
                else if (currentIndex < START_INDEX) {
                    track.style.transition = 'none';
                    currentIndex = currentIndex + n;
                    track.style.transform = `translateX(-${currentIndex * stepWidth}px)`;
                    void track.offsetWidth; // Ép browser reflow
                }

                updateDots();
            });

            // Nút Next: Luôn chuyển động từ phải sang trái
            if (btnNext) {
                btnNext.addEventListener('click', function () {
                    moveToIndex(currentIndex + 1);
                    resetAutoSlide();
                });
            }

            // Nút Prev: Luôn chuyển động từ trái sang phải
            if (btnPrev) {
                btnPrev.addEventListener('click', function () {
                    moveToIndex(currentIndex - 1);
                    resetAutoSlide();
                });
            }

            // Click vào các Dots
            dots.forEach((dot) => {
                dot.addEventListener('click', function () {
                    const targetRealIndex = parseInt(this.getAttribute('data-index'), 10);
                    moveToIndex(START_INDEX + targetRealIndex);
                    resetAutoSlide();
                });
            });

            let isHovered = false;

            // Dừng tự động trượt khi rê chuột (hover) vào slider
            function stopAutoSlide() {
                if (autoTimer) {
                    clearInterval(autoTimer);
                    autoTimer = null;
                }
            }

            // Tự động trượt liên tục mỗi 5 giây (chỉ chạy khi không hover)
            function startAutoSlide() {
                stopAutoSlide();
                if (isHovered) return;
                autoTimer = setInterval(function () {
                    moveToIndex(currentIndex + 1);
                }, 5000);
            }

            function resetAutoSlide() {
                stopAutoSlide();
                if (!isHovered) {
                    startAutoSlide();
                }
            }

            // Lắng nghe sự kiện hover để dừng/tiếp tục đếm tự chuyển
            const sliderWrapper = document.getElementById('hero-slider-wrapper');
            if (sliderWrapper) {
                sliderWrapper.addEventListener('mouseenter', function () {
                    isHovered = true;
                    stopAutoSlide();
                });

                sliderWrapper.addEventListener('mouseleave', function () {
                    isHovered = false;
                    startAutoSlide();
                });
            }

            // Đặt vị trí xuất phát ban đầu (không animation)
            function initPosition() {
                track.style.transition = 'none';
                const stepWidth = getStepWidth();
                track.style.transform = `translateX(-${currentIndex * stepWidth}px)`;
                void track.offsetWidth;
                updateDots();
            }

            // Lắng nghe resize cửa sổ để tính lại vị trí chính xác
            window.addEventListener('resize', function () {
                initPosition();
            });

            // Khởi tạo
            initPosition();
            startAutoSlide();
        });
    </script>
@endif
