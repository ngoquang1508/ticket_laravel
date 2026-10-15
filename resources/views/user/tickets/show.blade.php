@extends('layouts.app')

@section('title', 'Chi tiết vé - ' . $ticket->ticket_code)

@section('content')
    @php
        // Kiểm tra trạng thái vé đã sử dụng (qua trường is_used hoặc có thời gian checked_in_at)
        $isUsed = $ticket->is_used || !empty($ticket->checked_in_at);
        // Thời gian check-in cụ thể
        $checkInTime = $ticket->checked_in_at ?? $ticket->used_at;
        // Kiểm tra sự kiện đã kết thúc hoặc đơn hàng bị hủy/hết hạn
        $isEventEnded = $ticket->order->event && $ticket->order->event->ends_at && now()->gt($ticket->order->event->ends_at);
        $isExpired = !$isUsed && ($isEventEnded || in_array($ticket->order->status, ['expired', 'cancelled']));
    @endphp

    <div class="pt-8 pb-12">
        <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
            <!-- Header điều hướng -->
            <div class="mb-6 flex items-center justify-between">
                <button onclick="window.history.back()"
                    class="inline-flex items-center gap-2 text-sm font-medium text-gray-400 transition hover:text-white">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Quay lại
                </button>

                <button
                    onclick="navigator.clipboard.writeText('{{ route('tickets.public.show', $ticket->ticket_code) }}'); window.showAppToast('success', 'Thành công', 'Đã copy link chia sẻ');"
                    class="inline-flex items-center gap-2 rounded-lg bg-white/5 px-4 py-2 text-sm font-semibold text-gray-300 transition hover:bg-white/10 hover:text-white">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                    </svg>
                    Chia sẻ
                </button>
            </div>

            <!-- Thẻ hiển thị vé -->
            <div class="overflow-hidden rounded-2xl border border-dashed border-white/10 bg-[#292929] shadow-xl">
                <!-- Tên sự kiện -->
                <div class="pt-5 px-4 sm:px-6 text-center break-words w-full">
                    <h2 class="text-xl sm:text-2xl font-bold text-white">
                        <a href="{{ route('events.show', $ticket->order->event->slug) }}"
                            class="transition hover:text-green-500">
                            {{ $ticket->order->event->name }}
                        </a>
                    </h2>
                </div>

                <div class="p-6 sm:p-8">
                    <!-- Khu vực mã QR kèm Watermark/Overlay -->
                    <div class="mb-8 flex flex-col items-center border-b border-dashed border-white/10 pb-8 text-center">
                        <div
                            class="relative mb-4 overflow-hidden rounded-2xl bg-white p-3.5 sm:p-4 shadow-xl flex items-center justify-center">
                            <!-- Ảnh mã QR (bị làm mờ khi đã sử dụng hoặc hết hạn) -->
                            <img src="{{ (new \chillerlan\QRCode\QRCode)->render($ticket->ticket_code) }}"
                                alt="QR Code - {{ $ticket->ticket_code }}"
                                class="h-56 w-56 object-contain sm:h-64 sm:w-64 transition-all duration-300 {{ ($isUsed || $isExpired) ? 'blur-[3.5px] opacity-20 grayscale select-none pointer-events-none' : '' }}" />

                            {{-- Watermark xoay nghiêng khi vé ĐÃ SỬ DỤNG --}}
                            @if($isUsed)
                                <div
                                    class="absolute inset-0 flex items-center justify-center p-3 pointer-events-none select-none z-10">
                                    <div
                                        class="transform -rotate-12 rounded-2xl border-4 border-dashed border-red-600 bg-red-600/15 backdrop-blur-[2px] px-4 py-3 sm:px-6 sm:py-4 shadow-2xl text-center max-w-[92%] transition-transform">
                                        <div class="flex items-center justify-center gap-1.5 sm:gap-2 text-red-600">
                                            <span class="text-xl sm:text-2xl font-black tracking-widest uppercase">ĐÃ SỬ
                                                DỤNG</span>
                                        </div>
                                        @if($checkInTime)
                                            <p class="mt-1 text-xs sm:text-sm font-bold text-red-700 tracking-tight">
                                                Check-in lúc <br /> {{ $checkInTime->format('H:i - d/m/Y') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                {{-- Watermark xoay nghiêng khi vé HẾT HẠN --}}
                            @elseif($isExpired)
                                <div
                                    class="absolute inset-0 flex items-center justify-center p-3 pointer-events-none select-none z-10">
                                    <div
                                        class="transform -rotate-12 rounded-2xl border-4 border-dashed border-gray-600 bg-gray-600/20 backdrop-blur-[2px] px-5 py-3 sm:px-7 sm:py-4 shadow-2xl text-center max-w-[92%] transition-transform">
                                        <div class="flex items-center justify-center gap-1.5 sm:gap-2 text-gray-800">
                                            <svg class="h-6 w-6 sm:h-7 sm:w-7 shrink-0 stroke-[2.5]" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="text-xl sm:text-2xl font-black tracking-widest uppercase">HẾT
                                                HẠN</span>
                                        </div>
                                        <p class="mt-1 text-xs sm:text-sm font-semibold text-gray-700 tracking-tight">
                                            Sự kiện đã kết thúc
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Mã vé -->
                        <p class="font-mono text-xl sm:text-2xl font-bold tracking-widest text-white">
                            {{ $ticket->ticket_code }}
                        </p>
                    </div>

                    <!-- Thông tin sự kiện và vé -->
                    <div
                        class="grid grid-cols-1 gap-6 rounded-xl border border-white/5 bg-[#222222] p-5 sm:p-6 md:grid-cols-2">

                        <!-- Cột trái: Thời gian & Địa điểm -->
                        <div class="flex h-full flex-col justify-between space-y-5">
                            <div class="flex items-start gap-3">
                                <svg class="mt-0.5 h-5 w-5 shrink-0 text-blue-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-semibold text-white">Thời gian diễn ra</p>
                                    <p class="mt-0.5 text-sm text-gray-400">
                                        {{ $ticket->order->event->starts_at->format('H:i - d/m/Y') }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <svg class="mt-0.5 h-5 w-5 shrink-0 text-red-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-semibold text-white">Địa điểm tổ chức</p>
                                    <p class="mt-0.5 text-sm text-gray-300">{{ $ticket->order->event->location->name }}</p>
                                    <p class="mt-0.5 text-xs text-gray-500">{{ $ticket->order->event->location->address }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Cột phải: Chi tiết vé -->
                        <div
                            class="flex h-full flex-col justify-between space-y-4 border-t border-dashed border-white/10 pt-5 md:border-l md:border-t-0 md:pl-6 md:pt-0">
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Loại vé</p>
                                <p class="mt-1 font-semibold text-white">{{ $ticket->orderItem->ticketType->name }}</p>
                            </div>

                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Vị trí</p>
                                <p class="mt-1 font-semibold text-white">
                                    {{ $ticket->seat ? 'Ghế ' . $ticket->seat->seat_number : 'Tự do' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Giá vé</p>
                                <p class="mt-1 text-lg font-bold text-green-500">
                                    {{ number_format($ticket->orderItem->price, 0, ',', '.') }} ₫
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chân vé / Đường xé vé -->
                <div
                    class="relative flex items-center justify-between border-t border-dashed border-white/20 px-6 sm:px-8 py-4 sm:py-5 {{ $isUsed ? 'bg-red-500/10 text-red-400' : ($isExpired ? 'bg-gray-500/10 text-gray-400' : 'bg-green-500/10 text-green-400/90') }}">
                    <p class="w-full text-center text-xs font-medium">
                        @if($isUsed)
                            Vé này đã được sử dụng để check-in sự
                            kiện{{ $checkInTime ? ' lúc ' . $checkInTime->format('H:i - d/m/Y') : '' }}.
                        @elseif($isExpired)
                            Vé này đã hết hạn sử dụng do sự kiện đã kết thúc hoặc đơn hàng không còn hiệu lực.
                        @else
                            Vui lòng xuất trình mã QR này tại cửa kiểm soát để tham gia sự kiện.
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection