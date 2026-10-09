@extends('layouts.app')

@section('title', $event->name . ' - Sự kiện')

@section('content')
    @include('components.event-category-nav')
    <div class="min-h-screen text-white pt-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="relative flex flex-col md:flex-row bg-[#2b2b2b] rounded-2xl overflow-hidden shadow-2xl">

                <!-- Left Info -->
                <div class="w-full md:w-[38%] p-6 md:p-10 flex flex-col z-10 relative">
                    <h1 class="text-lg lg:text-xl font-bold text-white mb-8 uppercase leading-snug">{{ $event->name }}
                    </h1>

                    <div class="flex items-start gap-4 mb-6">
                        <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <div class="text-green-500 text-sm lg:text-base font-semibold">
                            {{ $event->starts_at->format('H:i, d \T\h\á\n\g m, Y') }} -
                            {{ $event->ends_at->format('H:i, d \T\h\á\n\g m, Y') }}
                        </div>
                    </div>

                    <div class="flex items-start gap-4 mb-8">
                        <svg class="w-5 h-5 text-white shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <div>
                            <div class="text-green-500 text-sm lg:text-base font-semibold">{{ $event->location->name }}
                            </div>
                            <div class="text-gray-400 text-sm mt-1.5 leading-relaxed">{{ $event->location->address }},
                                {{ $event->location->city }}
                            </div>
                        </div>
                    </div>

                    <div class="flex-grow"></div>

                    <div class="pt-6 border-t border-gray-400/50">
                        <div class="text-xl lg:text-2xl font-bold text-white mb-4 flex items-center gap-2">
                            Giá từ
                            <span class="text-green-500">
                                @if(isset($event->ticket_types_min_price) && $event->ticket_types_min_price > 0)
                                    {{ number_format($event->ticket_types_min_price, 0, ',', '.') }} đ
                                @else
                                    Miễn phí
                                @endif
                            </span>
                            @if(isset($event->ticket_types_min_price) && $event->ticket_types_min_price > 0)
                                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                    </path>
                                </svg>
                            @endif
                        </div>
                        @if(now() < $event->starts_at)
                            <a href="{{ route('events.book', $event->slug) }}" class="w-full block text-center py-3.5 rounded-lg font-bold transition duration-300 cursor-pointer text-white bg-[#1db954] hover:bg-white hover:text-black">
                                Mua vé ngay
                            </a>
                        @elseif(now() <= $event->ends_at)
                            <button class="w-full py-3.5 rounded-lg font-bold transition duration-300 cursor-not-allowed text-white bg-[#1db954]" disabled>
                                Đang diễn ra
                            </button>
                        @else
                            <button class="w-full py-3.5 rounded-lg font-bold transition duration-300 cursor-not-allowed text-[#2b2b2b] bg-gray-500/80" disabled>
                                Sự kiện đã kết thúc
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Right Image -->
                <div class="w-full md:w-[62%] min-h-[300px] md:min-h-[500px] relative order-first md:order-last">
                    @if($event->image)
                        <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->name }}"
                            class="absolute inset-0 w-full h-full object-cover">
                    @else
                        <div
                            class="absolute inset-0 w-full h-full bg-gradient-to-br from-gray-700 to-gray-900 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 text-gray-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                            </svg>
                        </div>
                    @endif
                </div>

                <!-- Cutout Overlay & Dashed Line -->
                <!-- Only visible on md+ -->
                <div
                    class="hidden md:block absolute top-0 bottom-0 left-[38%] w-10 -translate-x-1/2 pointer-events-none z-20">
                    <!-- Top Circle -->
                    <div class="absolute top-[-20px] left-1/2 w-10 h-10 -translate-x-1/2 rounded-full bg-[#222222]"></div>
                    <!-- Bottom Circle -->
                    <div class="absolute bottom-[-20px] left-1/2 w-10 h-10 -translate-x-1/2 rounded-full bg-[#222222]">
                    </div>
                    <!-- Dashed line -->
                    <div
                        class="absolute top-6 bottom-6 left-1/2 border-l-[3px] border-dashed border-gray-700 -translate-x-1/2">
                    </div>
                </div>

            </div>

            <!-- Description Section -->
            <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-8">
                    <div class="bg-[#2b2b2b] rounded-2xl p-6 md:p-8 shadow-xl">
                        <h3 class="text-xl font-bold mb-6 text-green-500 uppercase flex items-center gap-2">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Giới thiệu sự kiện
                        </h3>
                        <div class="text-gray-300 leading-relaxed space-y-4 text-base">
                            @if($event->description)
                                {!! nl2br(e($event->description)) !!}
                            @else
                                <p class="text-gray-500 italic">Chưa có thông tin giới thiệu cho sự kiện này.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Lịch diễn Section -->
                    <div class="bg-[#2b2b2b] rounded-2xl p-6 md:p-8 shadow-xl">
                        <!-- Header -->
                        <div class="mb-6">
                            <h3 class="text-xl font-bold text-green-500 flex items-center gap-2">
                                Lịch diễn
                            </h3>
                        </div>

                        <!-- Schedule Row Accordion -->
                        <div class="schedule-accordion space-y-4">
                            <div
                                class="flex flex-col bg-[#3a3a3a] rounded-xl overflow-hidden transition-all duration-300 border border-gray-600/50">
                                <div
                                    class="w-full flex flex-col sm:flex-row items-start sm:items-center justify-between p-4 cursor-pointer schedule-header hover:bg-[#444] transition">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-5 h-5 text-gray-400 arrow transition-transform duration-300"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7"></path>
                                        </svg>
                                        <span class="text-white font-bold text-base">
                                            {{ \Carbon\Carbon::parse($event->starts_at)->format('H:i') }} -
                                            {{ \Carbon\Carbon::parse($event->ends_at)->format('H:i, d \T\h\á\n\g m, Y') }}
                                        </span>
                                    </div>
                        @if(now() < $event->starts_at)
                            <a href="{{ route('events.book', $event->slug) }}" class="w-full inline-block text-center py-3.5 rounded-lg font-bold transition duration-300 cursor-pointer text-white bg-[#1db954] hover:bg-white hover:text-black">
                                Mua vé ngay
                            </a>
                        @elseif(now() <= $event->ends_at)
                            <button class="w-full md:w-auto md:px-6 px-3 py-3.5 mt-4 md:mt-0 rounded-lg font-bold transition duration-300 cursor-not-allowed text-white bg-[#1db954]" disabled>
                                Đang diễn ra
                            </button>
                        @else
                            <button class="w-full md:w-auto md:px-6 px-3 py-3.5 mt-4 md:mt-0 rounded-lg font-bold transition duration-300 cursor-not-allowed text-[#2b2b2b] bg-gray-500/80" disabled>
                                Sự kiện đã kết thúc
                            </button>
                        @endif
                                </div>

                                <!-- Accordion Content -->
                                <div
                                    class="schedule-content max-h-0 overflow-hidden opacity-0 transition-all duration-300 ease-in-out">
                                    <div class="border-t border-gray-600/50 p-5 bg-[#3a3a3a]">
                                        <h4 class="text-white font-bold mb-4 text-base">
                                            Thông tin vé
                                        </h4>

                                        <div class="space-y-3">
                                            @forelse($event->ticketTypes as $ticket)
                                                <div
                                                    class="flex items-center justify-between bg-[#2b2b2b] rounded-lg p-4 border border-gray-600/30">
                                                    <span class="text-gray-300 font-bold uppercase text-sm">
                                                        {{ $ticket->name }}
                                                    </span>

                                                    <span class="text-green-500 font-bold">
                                                        {{ number_format($ticket->price, 0, ',', '.') }} đ
                                                    </span>
                                                </div>
                                            @empty
                                                <div class="text-gray-400 italic">
                                                    Chưa có thông tin vé cho sự kiện này.
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Info -->
                <div class="space-y-6">
                    <div class="bg-[#2b2b2b] rounded-2xl p-6 md:p-8 shadow-xl">
                        <h3 class="text-lg font-bold mb-5 text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                </path>
                            </svg>
                            Chính sách & Quy định
                        </h3>
                        <ul class="text-sm text-gray-400 space-y-3">
                            <li class="flex items-start gap-2">
                                <span class="text-green-500 mt-0.5">•</span>
                                <span>Vé mua rồi không được hoàn trả dưới mọi hình thức.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-green-500 mt-0.5">•</span>
                                <span>Vui lòng đến trước 30 phút để check-in và nhận chỗ.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-green-500 mt-0.5">•</span>
                                <span>Ban tổ chức có quyền từ chối sự tham gia của bất kỳ khán giả nào không tuân thủ quy
                                    định.</span>
                            </li>
                        </ul>
                    </div>

                    <div class="bg-[#2b2b2b] rounded-2xl p-6 md:p-8 shadow-xl">
                        <h3 class="text-lg font-bold mb-5 text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z">
                                </path>
                            </svg>
                            Chia sẻ sự kiện
                        </h3>
                        <div class="flex gap-4">
                            <a href="#"
                                class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center hover:bg-blue-600 transition"
                                title="Chia sẻ Facebook">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                                </svg>
                            </a>
                            <a href="#"
                                class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center hover:bg-sky-500 transition"
                                title="Chia sẻ Twitter">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
                                </svg>
                            </a>
                            <button
                                class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center hover:bg-gray-500 transition"
                                onclick="navigator.clipboard.writeText(window.location.href); alert('Đã sao chép đường dẫn!');"
                                title="Sao chép liên kết">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1">
                                    </path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Danh sách các sự kiện liên quan -->
            <div class="mt-12 pt-6 border-t border-gray-400/50 text-xl text-green-500">
                <x-event-section :events="$relatedEvents" title="Có thể bạn cũng thích" type="related" :is-past="false"
                    :category-slug="$currentCategory ? $currentCategory->slug : null" :center-title="true" />
            </div>

        </div>
    </div>
@endsection