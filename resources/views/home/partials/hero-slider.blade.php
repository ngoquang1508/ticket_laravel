@props(['events'])

{{-- Hero Banner Slider: Hiển thị slider ảnh với infinite seamless loop linh hoạt cho mọi số lượng sự kiện --}}
@if($events->isNotEmpty())
    @php
        $rawEvents = $events->values();
        $realCount = $rawEvents->count();
        $isSlider = $realCount > 1;

        if ($isSlider) {
            if ($realCount === 2) {
                // Nhân đôi danh sách để đủ 4 card, đảm bảo hiệu ứng trượt vô hạn 2 ảnh song song không bị giật
                $baseEvents = $rawEvents->concat($rawEvents)->values();
                $cycleLength = 4;
            } else {
                $baseEvents = $rawEvents;
                $cycleLength = $realCount;
            }

            // Clone 2 item ở cuối đưa lên đầu, và 2 item ở đầu đưa xuống cuối
            $cloneBefore = $baseEvents->slice(-2)->values();
            $cloneAfter = $baseEvents->take(2)->values();
            $displayEvents = $cloneBefore->concat($baseEvents)->concat($cloneAfter);
        } else {
            $displayEvents = $rawEvents;
            $cycleLength = 1;
        }
    @endphp

    <section class="relative pt-6 overflow-hidden" id="hero-slider-section">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="relative group" id="hero-slider-wrapper">
                {{-- Khung nhìn slider --}}
                <div class="overflow-hidden rounded-2xl" id="hero-viewport">
                    {{-- Dải track chứa các card --}}
                    <div class="flex gap-4 will-change-transform {{ !$isSlider ? 'justify-center' : '' }}" id="hero-track">
                        @foreach($displayEvents as $index => $event)
                            <div class="hero-card-item shrink-0 {{ $isSlider ? 'w-full md:w-[calc(50%-8px)]' : 'w-full' }}">
                                <div class="relative {{ $isSlider ? 'aspect-[16/9]' : 'aspect-[16/9] md:aspect-[21/9]' }} w-full overflow-hidden rounded-2xl bg-gray-900 border border-white/10 shadow-2xl group/card">
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
                                    <div class="absolute bottom-3 left-3 z-10 sm:bottom-5 sm:left-5">
                                        <a href="{{ route('events.show', $event->slug) }}"
                                            class="inline-flex items-center justify-center rounded-md bg-white px-3 py-1.5 sm:px-4 sm:py-2 text-xs sm:text-sm font-bold text-gray-900 shadow-md transition hover:bg-gray-200">
                                            Xem chi tiết
                                        </a>
                                    </div>

                                    {{-- Nút play video góc dưới bên phải --}}
                                    <div class="absolute bottom-3 right-3 z-10 sm:bottom-5 sm:right-5">
                                        <a href="{{ route('events.show', $event->slug) }}"
                                            class="flex h-7 w-7 sm:h-9 sm:w-9 items-center justify-center rounded-md bg-black/60 text-white backdrop-blur-sm transition hover:bg-black/90"
                                            aria-label="Xem chi tiết sự kiện">
                                            <x-icons.play class="h-3.5 w-3.5 sm:h-4 sm:w-4" />
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if($isSlider)
                    {{-- Nút cuộn trái: Luôn luôn chuyển động từ trái sang phải --}}
                    <button type="button" id="hero-prev"
                        class="flex absolute left-3 top-1/2 -translate-y-1/2 h-8 w-8 sm:h-10 sm:w-10 items-center justify-center rounded-full bg-black/60 hover:bg-black/90 text-white/90 backdrop-blur-sm transition z-20 shadow-lg cursor-pointer"
                        aria-label="Slide trước">
                        <x-icons.chevron-left class="h-4 w-4 sm:h-5 sm:w-5" />
                    </button>

                    {{-- Nút cuộn phải: Luôn luôn chuyển động từ phải sang trái --}}
                    <button type="button" id="hero-next"
                        class="flex absolute right-3 top-1/2 -translate-y-1/2 h-8 w-8 sm:h-10 sm:w-10 items-center justify-center rounded-full bg-black/60 hover:bg-black/90 text-white/90 backdrop-blur-sm transition z-20 shadow-lg cursor-pointer"
                        aria-label="Slide tiếp theo">
                        <x-icons.chevron-right class="h-4 w-4 sm:h-5 sm:w-5" />
                    </button>

                    {{-- Dots phân trang --}}
                    <div class="mt-3 flex justify-center gap-1.5" id="hero-dots">
                        @for($i = 0; $i < $realCount; $i++)
                            <button type="button" data-index="{{ $i }}"
                                class="h-1 rounded-full transition-all duration-300 {{ $i === 0 ? 'w-6 bg-emerald-500' : 'w-2 bg-white/30' }}"
                                aria-label="Đến ảnh {{ $i + 1 }}"></button>
                        @endfor
                    </div>
                @endif
            </div>
        </div>
    </section>

    @if($isSlider)
        {{-- Script Infinite Seamless Loop: Hoạt động liền mạch với bất kỳ số lượng sự kiện nào >= 2 --}}
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const track = document.getElementById('hero-track');
                const btnPrev = document.getElementById('hero-prev');
                const btnNext = document.getElementById('hero-next');
                const dots = document.querySelectorAll('#hero-dots button');
                const realCount = {{ $realCount }};
                const cycleLength = {{ $cycleLength }};
                const START_INDEX = 2; // Vị trí phần tử thật đầu tiên trong dải track
                let currentIndex = START_INDEX;
                let isTransitioning = false;
                let autoTimer = null;

                function getStepWidth() {
                    const firstCard = track.children[0];
                    return firstCard ? firstCard.offsetWidth + 16 : 0;
                }

                // Cập nhật trạng thái hiển thị của các dots
                function updateDots() {
                    let realIndex = ((currentIndex - START_INDEX) % realCount + realCount) % realCount;

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
                    if (currentIndex >= START_INDEX + cycleLength) {
                        track.style.transition = 'none';
                        currentIndex = currentIndex - cycleLength;
                        track.style.transform = `translateX(-${currentIndex * stepWidth}px)`;
                        void track.offsetWidth; // Ép browser reflow
                    }
                    // Nếu vừa trượt qua clone phía trước (Prev qua item đầu về cuối)
                    else if (currentIndex < START_INDEX) {
                        track.style.transition = 'none';
                        currentIndex = currentIndex + cycleLength;
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

                // Khởi tạo ban đầu
                initPosition();
                startAutoSlide();
            });
        </script>
    @endif
@endif
