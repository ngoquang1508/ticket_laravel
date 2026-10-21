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
                        <x-icons.calendar class="w-5 h-5 shrink-0 mt-0.5" />
                        <div class="text-green-500 text-sm lg:text-base font-semibold">
                            {{ $event->starts_at->format('H:i, d \T\h\á\n\g m, Y') }} -
                            {{ $event->ends_at->format('H:i, d \T\h\á\n\g m, Y') }}
                        </div>
                    </div>

                    <div class="flex items-start gap-4 mb-8">
                        <x-icons.location class="w-5 h-5 text-white shrink-0 mt-0.5" />
                        <div>
                            <div class="text-green-500 text-sm lg:text-base font-semibold">{{ $event->location->name }}
                            </div>
                            <div class="text-gray-400 text-sm mt-1.5 leading-relaxed">{{ $event->location->address }},
                                {{ $event->location->city }}
                            </div>
                        </div>
                    </div>

                    <!-- Phương thức thanh toán áp dụng -->
                    <div class="flex items-start gap-4 mb-8">
                        <x-icons.credit-card class="w-5 h-5 text-white shrink-0 mt-0.5" />
                        <div class="flex-1 min-w-0">
                            <div class="text-green-500 text-sm lg:text-base font-semibold mb-2.5">
                                Phương thức thanh toán
                            </div>

                            <div class="space-y-2 text-xs sm:text-sm">
                                <!-- Thanh toán Online -->
                                <div class="flex items-center justify-between rounded-lg bg-white/5 px-3 py-2.5">
                                    <div class="flex items-center gap-2">
                                        <span class="h-2 w-2 shrink-0 rounded-full bg-blue-400"></span>
                                        <span class="font-medium text-gray-200">Online (VNPay)</span>
                                    </div>

                                    <span class="text-[11px] text-blue-400">
                                        Hỗ trợ 24/7
                                    </span>
                                </div>

                                <!-- Thanh toán Trực tiếp -->
                                <div class="flex items-center justify-between gap-3 rounded-lg bg-white/5 px-3 py-2.5">
                                    <div class="flex min-w-0 items-center gap-2">
                                        <span class="h-2 w-2 shrink-0 rounded-full
                                {{ $event->canPayDirect() ? 'bg-amber-400' : 'bg-red-400' }}">
                                        </span>

                                        <span class="font-medium text-gray-200">
                                            Trực tiếp / Chuyển khoản
                                        </span>
                                    </div>

                                    @if($event->canPayDirect())
                                        <span class="shrink-0 text-[11px] text-gray-400">
                                            Ngừng: <span class="font-medium text-amber-400">
                                                {{ $event->direct_payment_cutoff_at->format('H:i, d/m/Y') }}
                                            </span>
                                        </span>
                                    @else
                                        <span class="shrink-0 text-[11px] font-medium text-red-400">
                                            Đã ngừng nhận
                                        </span>
                                    @endif
                                </div>
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
                                <x-icons.chevron-right class="w-5 h-5 text-green-500" />
                            @endif
                        </div>
                        @if(!$event->isSaleStarted())
                            <div class="w-full py-3 px-4 rounded-lg font-bold text-center bg-amber-500/10 border border-amber-500/30 text-amber-400">
                                <span class="block text-sm sm:text-base">Sắp mở bán</span>
                                <span class="block text-xs font-normal text-gray-300 mt-0.5">
                                    Mở bán lúc: {{ $event->ticket_sale_starts_at->format('H:i, d/m/Y') }}
                                </span>
                            </div>
                        @elseif(now() < $event->starts_at)
                            <a href="{{ route('events.book', $event->slug) }}"
                                class="w-full block text-center py-3.5 rounded-lg font-bold transition duration-300 cursor-pointer text-white bg-[#1db954] hover:bg-white hover:text-black">
                                Mua vé ngay
                            </a>
                        @elseif(now() <= $event->ends_at)
                            <button
                                class="w-full py-3.5 rounded-lg font-bold transition duration-300 cursor-not-allowed text-white bg-[#1db954]"
                                disabled>
                                Đang diễn ra
                            </button>
                        @else
                            <button
                                class="w-full py-3.5 rounded-lg font-bold transition duration-300 cursor-not-allowed text-[#2b2b2b] bg-gray-500/80"
                                disabled>
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
                            <x-icons.ticket class="h-20 w-20 text-gray-500" />
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
                            <x-icons.info class="w-6 h-6" />
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
                                        <x-icons.chevron-right class="w-5 h-5 text-gray-400 arrow transition-transform duration-300" />
                                        <span class="text-white font-bold text-base">
                                            {{ \Carbon\Carbon::parse($event->starts_at)->format('H:i') }} -
                                            {{ \Carbon\Carbon::parse($event->ends_at)->format('H:i, d \T\h\á\n\g m, Y') }}
                                        </span>
                                    </div>
                                    @if(!$event->isSaleStarted())
                                        <div class="w-full sm:w-auto sm:px-5 px-3 py-2 mt-4 sm:mt-0 text-center rounded-lg font-bold bg-amber-500/10 border border-amber-500/30 text-amber-400 text-xs sm:text-sm">
                                            <span>Sắp mở bán</span>
                                            <span class="block sm:inline sm:ml-1 text-[11px] font-normal text-gray-300">
                                                ({{ $event->ticket_sale_starts_at->format('H:i, d/m/Y') }})
                                            </span>
                                        </div>
                                    @elseif(now() < $event->starts_at)
                                        <a href="{{ route('events.book', $event->slug) }}" onclick="event.stopPropagation()"
                                            class="w-full sm:w-auto sm:px-6 px-3 py-3 mt-4 sm:mt-0 inline-block text-center rounded-lg font-bold transition duration-300 cursor-pointer text-white bg-[#1db954] hover:bg-white hover:text-black">
                                            Mua vé ngay
                                        </a>
                                    @elseif(now() <= $event->ends_at)
                                        <button onclick="event.stopPropagation()"
                                            class="w-full sm:w-auto sm:px-6 px-3 py-3 mt-4 sm:mt-0 rounded-lg font-bold transition duration-300 cursor-not-allowed text-white bg-[#1db954]"
                                            disabled>
                                            Đang diễn ra
                                        </button>
                                    @else
                                        <button onclick="event.stopPropagation()"
                                            class="w-full sm:w-auto sm:px-6 px-3 py-3 mt-4 sm:mt-0 rounded-lg font-bold transition duration-300 cursor-not-allowed text-[#2b2b2b] bg-gray-500/80"
                                            disabled>
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
                            <x-icons.shield-check class="w-5 h-5 text-green-500" />
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
                            <x-icons.share class="w-5 h-5 text-green-500" />
                            Chia sẻ sự kiện
                        </h3>
                        <div class="flex gap-4">
                            <a href="#"
                                class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center hover:bg-blue-600 transition"
                                title="Chia sẻ Facebook">
                                <x-icons.facebook class="w-4 h-4 text-white" />
                            </a>
                            <a href="#"
                                class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center hover:bg-sky-500 transition"
                                title="Chia sẻ Twitter">
                                <x-icons.twitter class="w-4 h-4 text-white" />
                            </a>
                            <button
                                class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center hover:bg-gray-500 transition cursor-pointer"
                                onclick="navigator.clipboard.writeText(window.location.href); alert('Đã sao chép đường dẫn!');"
                                title="Sao chép liên kết">
                                <x-icons.link class="w-4 h-4 text-white" />
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