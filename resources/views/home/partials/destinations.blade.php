@props(['destinations'])

{{-- Điểm đến thú vị (Ảnh 3 - 3 thành phố chính: TP.HCM, Hà Nội, Đà Nẵng và Vị trí khác) --}}
@if(!empty($destinations))
    <section class="py-4">
        {{-- Tiêu đề section --}}
        <div class="mb-5 flex items-center justify-between">
            <h2 class="text-lg sm:text-xl font-bold text-white flex items-center gap-2">
                <x-icons.map class="w-5 h-5 text-[#FFD530]" />
                <span>Điểm đến thú vị</span>
            </h2>
        </div>

        {{-- Lưới 4 thẻ điểm đến lớn --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-5">
            @foreach($destinations as $dest)
                @php
                    $destUrl = !empty($dest['location']) && $dest['location'] === 'other'
                        ? route('events.index', ['location' => 'other'])
                        : route('events.index', ['q' => $dest['query']]);
                @endphp
                <a href="{{ $destUrl }}"
                    class="group relative aspect-[3/4] sm:aspect-[4/5] w-full overflow-hidden rounded-2xl border border-white/10 bg-gray-900 shadow-xl transition-colors duration-300 hover:border-emerald-500/50">

                    {{-- Ảnh phong cảnh điểm đến --}}
                    <img src="{{ $dest['image'] }}" alt="{{ $dest['name'] }}"
                        class="h-full w-full object-cover transition-transform duration-500 ease-out group-hover:scale-105">

                    {{-- Gradient tối toàn ảnh --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-emerald-950/90 via-black/40 to-transparent"></div>

                    {{-- Gradient xanh ở chân ảnh --}}
                    <div
                        class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-[#0e3a1f]/95 via-[#0e3a1f]/40 to-transparent opacity-90 transition-opacity duration-300 group-hover:opacity-100">
                    </div>

                    {{-- Tên thành phố và số sự kiện --}}
                    <div class="absolute bottom-4 left-4 right-4 z-10">
                        <h3
                            class="text-sm sm:text-base md:text-lg font-bold text-white drop-shadow-md transition-colors duration-300 group-hover:text-emerald-300">
                            {{ $dest['name'] }}
                        </h3>

                        @if($dest['count'] > 0)
                            <p class="mt-0.5 text-xs text-emerald-200">
                                {{ $dest['count'] }} sự kiện đang diễn ra
                            </p>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    </section>
@endif