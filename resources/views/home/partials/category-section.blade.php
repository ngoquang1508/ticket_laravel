@props(['categories'])

{{-- Các khối sự kiện theo từng Thể loại (Ảnh 1 - Thể thao, Âm nhạc, Giải trí...) --}}
@if(!empty($categories))
    @foreach($categories as $category)
        {{-- Chỉ hiển thị danh mục khi có từ 4 sự kiện trở lên --}}
        @if($category->events && $category->events->count() >= 4)
            <section class="py-4">
                {{-- Tiêu đề Danh mục kèm icon và mũi tên > --}}
                <div class="mb-4 flex items-center justify-between">
                    <a href="{{ route('events.index', $category->slug) }}"
                        class="group inline-flex items-center gap-2 text-xl sm:text-2xl font-extrabold text-white hover:text-emerald-400 transition-colors">
                        <span>{{ $category->name }}</span>
                        <svg class="h-5 w-5 text-gray-400 group-hover:text-emerald-400 group-hover:translate-x-1 transition-all"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>

                {{-- Lưới thẻ sự kiện thuộc danh mục --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                    @foreach($category->events as $event)
                        @include('home.partials.event-card-compact', ['event' => $event])
                    @endforeach
                </div>
            </section>
        @endif
    @endforeach
@endif