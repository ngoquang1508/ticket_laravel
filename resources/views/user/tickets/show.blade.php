@extends('layouts.app')

@section('title', 'Chi tiết vé - ' . $ticket->ticket_code)

@section('content')
<div class="pt-8 pb-12">
    <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 flex items-center justify-between">
            <button onclick="window.history.back()" class="inline-flex items-center gap-2 text-sm font-medium text-gray-400 transition hover:text-white">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Quay lại
            </button>
            
            <button onclick="navigator.clipboard.writeText('{{ route('tickets.public.show', $ticket->ticket_code) }}'); window.showAppToast('success', 'Thành công', 'Đã copy link chia sẻ');" class="inline-flex items-center gap-2 rounded-lg bg-white/5 px-4 py-2 text-sm font-semibold text-gray-300 transition hover:bg-white/10 hover:text-white">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                Chia sẻ
            </button>
        </div>

        <!-- Ticket Card -->
        <div class="overflow-hidden rounded-2xl border border-dashed border-white/10 bg-[#292929] shadow-xl">
            <!-- Event Name -->
            <div class="pt-4 px-4 text-center break-words w-full">
                <h2 class="text-xl font-bold text-white"><a href="{{ route('events.show', $ticket->order->event->slug) }}" class="transition hover:text-green-500">{{ $ticket->order->event->name }}</a></h2>
            </div>

            <div class="p-6 sm:p-8">
                <!-- QR Code Section -->
                <div class="mb-8 flex flex-col items-center border-b border-dashed border-white/10 pb-8 text-center">
                    <div class="mb-4 overflow-hidden rounded-xl bg-white p-3 ">
                        <img src="{{ (new \chillerlan\QRCode\QRCode)->render($ticket->ticket_code) }}" alt="QR Code" class="h-56 w-56 object-contain sm:h-64 sm:w-64" />
                    </div>
                    <p class="font-mono text-xl font-bold tracking-widest text-white">{{ $ticket->ticket_code }}</p>
                </div>

                <!-- Event & Ticket Info -->
                <div class="grid grid-cols-1 gap-6 rounded-xl border border-white/5 bg-[#222222] p-5 sm:p-6 md:grid-cols-2">
                    
                    <!-- Left: Time & Location -->
                    <div class="flex h-full flex-col justify-between space-y-5">
                        <div class="flex items-start gap-3">
                            <svg class="mt-0.5 h-5 w-5 shrink-0 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <div>
                                <p class="text-sm font-semibold text-white">Thời gian diễn ra</p>
                                <p class="mt-0.5 text-sm text-gray-400">{{ $ticket->order->event->starts_at->format('H:i - d/m/Y') }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-3">
                            <svg class="mt-0.5 h-5 w-5 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <div>
                                <p class="text-sm font-semibold text-white">Địa điểm tổ chức</p>
                                <p class="mt-0.5 text-sm text-gray-300">{{ $ticket->order->event->location->name }}</p>
                                <p class="mt-0.5 text-xs text-gray-500">{{ $ticket->order->event->location->address }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Ticket Details -->
                    <div class="flex h-full flex-col justify-between space-y-4 border-t border-dashed border-white/10 pt-5 md:border-l md:border-t-0 md:pl-6 md:pt-0">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Loại vé</p>
                            <p class="mt-1 font-semibold text-white">{{ $ticket->orderItem->ticketType->name }}</p>
                        </div>

                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Vị trí</p>
                            <p class="mt-1 font-semibold text-white">{{ $ticket->seat ? 'Ghế ' . $ticket->seat->seat_number : 'Tự do' }}</p>
                        </div>

                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Giá vé</p>
                            <p class="mt-1 text-lg font-bold text-green-500">{{ number_format($ticket->orderItem->price, 0, ',', '.') }} ₫</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tear line -->
            <div class="relative flex items-center justify-between border-t border-dashed border-white/20 bg-green-500/10 px-8 py-5">
                <p class="w-full text-center text-xs font-medium text-green-400/80">Vui lòng xuất trình mã QR này tại cửa kiểm soát để tham gia sự kiện.</p>
            </div>
        </div>
    </div>
</div>
@endsection
