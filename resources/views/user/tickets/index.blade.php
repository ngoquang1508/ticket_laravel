@extends('layouts.app')

@section('title', 'Vé của tôi')

@section('content')
    <div class="pt-8">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">

            <!-- Filters -->
            <div class="mb-6 flex overflow-x-auto">
                <a href="{{ route('user.tickets') }}"
                    class="{{ !$status ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-400 hover:border-white hover:text-white' }} whitespace-nowrap border-b-2 px-1 py-2 text-sm font-medium mr-8 transition">
                    Tất cả
                </a>
                <a href="{{ route('user.tickets', ['status' => 'completed']) }}"
                    class="{{ $status === 'completed' ? 'border-green-500 text-green-500' : 'border-transparent text-gray-400 hover:border-white hover:text-white' }} whitespace-nowrap border-b-2 px-1 py-2 text-sm font-medium mr-8 transition">
                    Thành công
                </a>
                <a href="{{ route('user.tickets', ['status' => 'pending']) }}"
                    class="{{ $status === 'pending' ? 'border-amber-500 text-amber-600' : 'border-transparent text-gray-400 hover:border-white hover:text-white' }} whitespace-nowrap border-b-2 px-1 py-2 text-sm font-medium mr-8 transition">
                    Chờ xử lý
                </a>
                <a href="{{ route('user.tickets', ['status' => 'cancelled']) }}"
                    class="{{ $status === 'cancelled' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-400 hover:border-white hover:text-white' }} whitespace-nowrap border-b-2 px-1 py-2 text-sm font-medium transition">
                    Đã hủy
                </a>
            </div>

            <!-- Orders List -->
            <div class="space-y-4">
                @forelse($orders as $order)

                    <div
                        class="overflow-hidden rounded-2xl border border-dashed border-white/10 bg-[#292929] shadow-sm transition hover:border-white/20 hover:shadow-md">

                        <!-- Card Header -->
                        <div class="flex items-start justify-between gap-4 border-b border-dashed border-white/10 px-5 py-4">

                            <div class="min-w-0">
                                <p class="mb-1 text-xs font-medium uppercase tracking-wide text-gray-300/50">
                                    Sự kiện
                                </p>

                                <h3 class="truncate text-lg font-bold text-white">
                                    <a href="{{ route('events.show', $order->event->slug) }}"
                                        class="transition hover:text-green-500">
                                        {{ $order->event->name }}
                                    </a>
                                </h3>
                            </div>

                            <!-- Status -->
                            @if($order->status === 'completed')

                                <span
                                    class="inline-flex shrink-0 items-center gap-1.5 rounded-full
                                                                                 bg-emerald-500/10 px-3 py-1.5 text-xs font-semibold
                                                                                 text-emerald-400 ring-1 ring-inset ring-emerald-500/20">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    Thành công
                                </span>

                            @elseif($order->status === 'pending')

                                <span
                                    class="inline-flex shrink-0 items-center gap-1.5 rounded-full
                                                                                 bg-amber-500/10 px-3 py-1.5 text-xs font-semibold
                                                                                 text-amber-400 ring-1 ring-inset ring-amber-500/20">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Chờ xử lý
                                </span>

                            @else

                                <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full
                                                                                 bg-red-500/10 px-3 py-1.5 text-xs font-semibold
                                                                                 text-red-400 ring-1 ring-inset ring-red-500/20">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Đã hủy
                                </span>

                            @endif

                        </div>


                        <!-- Card Body -->
                        <div class="px-5 py-4">

                            <!-- Order Info -->
                            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">

                                <!-- Order Code -->
                                <div>
                                    <p class="mb-1 text-xs text-gray-300/50">
                                        Mã đơn hàng
                                    </p>

                                    <p class="font-mono text-sm font-semibold text-gray-200 flex items-center gap-2">
                                        {{ $order->order_code }}
                                        <svg onclick="navigator.clipboard.writeText('{{ $order->order_code }}'); window.showAppToast('success', 'Thành công', 'Đã copy mã đơn hàng');" class="h-4 w-4 text-gray-300/50 hover:text-white cursor-pointer" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    </p>
                                </div>

                                <!-- Date -->
                                <div>
                                    <p class="mb-1 text-xs text-gray-300/50">
                                        Thời gian sự kiện
                                    </p>

                                    <div class="flex items-center gap-2 text-sm text-gray-200">
                                        <svg class="h-4 w-4 text-gray-300/50" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>

                                        {{ $order->event->starts_at->format('d/m/Y H:i') }}
                                    </div>
                                </div>

                                <!-- Quantity -->
                                <div>
                                    <p class="mb-1 text-xs text-gray-300/50">
                                        Số lượng
                                    </p>

                                    <div class="flex items-center gap-2 text-sm text-gray-200">
                                        <svg class="h-4 w-4 text-gray-300/50" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                        </svg>

                                        {{ $order->items->sum('quantity') }} vé
                                    </div>
                                </div>

                                <!-- Total -->
                                <div>
                                    <p class="mb-1 text-xs text-gray-300/50">
                                        Tổng tiền
                                    </p>

                                    <p class="text-sm font-bold text-green-500">
                                        {{ number_format($order->total_amount, 0, ',', '.') }} ₫
                                    </p>
                                </div>

                            </div>


                            <!-- Actions -->
                            <div class="mt-5 flex flex-wrap items-center justify-end gap-2 @if ($order->status !== 'cancelled') border-t border-dashed border-white/10 pt-4 @endif">

                                @if($order->status === 'completed')

                                    <button type="button" onclick="
                                        const content = document.getElementById('ticket-details-{{ $order->id }}');
                                        const span = document.getElementById('btn-text-{{ $order->id }}');
                                        const icon = document.getElementById('btn-icon-{{ $order->id }}');
                                        if (content.classList.contains('grid-rows-[0fr]')) {
                                            content.classList.remove('grid-rows-[0fr]', 'opacity-0');
                                            content.classList.add('grid-rows-[1fr]', 'opacity-100');
                                            span.innerText = 'Ẩn bớt';
                                            icon.classList.add('rotate-180');
                                        } else {
                                            content.classList.remove('grid-rows-[1fr]', 'opacity-100');
                                            content.classList.add('grid-rows-[0fr]', 'opacity-0');
                                            span.innerText = 'Xem chi tiết vé';
                                            icon.classList.remove('rotate-180');
                                        }
                                    " class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-white transition cursor-pointer">
                                        <span id="btn-text-{{ $order->id }}">Xem chi tiết vé</span>
                                        <svg id="btn-icon-{{ $order->id }}" class="h-4 w-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>

                                @elseif($order->status === 'pending')

                                    @if($order->payment_method === 'direct')

                                        <a href="{{ route('orders.instruction', $order) }}"
                                            class="rounded-lg border border-emerald-500/20
                                                                                                      bg-emerald-500/10 px-4 py-2 text-xs font-semibold
                                                                                                      text-emerald-400 transition hover:bg-emerald-500/20">
                                            Hướng dẫn thanh toán
                                        </a>

                                    @else

                                        <a href="{{ route('orders.checkout', $order) }}"
                                            class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold
                                                                                                      text-white transition hover:bg-blue-500">
                                            Thanh toán ngay
                                        </a>

                                    @endif

                                    <form action="{{ route('orders.cancel', $order) }}" method="POST"
                                        onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này không? Vé đang giữ sẽ bị hủy bỏ.');">
                                        @csrf

                                        <button type="submit" class="rounded-lg border border-white/10 bg-white/5
                                                                                           px-4 py-2 text-xs font-semibold text-gray-300
                                                                                           transition hover:border-red-500/30
                                                                                           hover:bg-red-500/10 hover:text-red-400">
                                            Hủy đơn hàng
                                        </button>
                                    </form>

                                @endif

                            </div>

                        </div>
                        
                        <!-- Ticket Details Dropdown -->
                        @if($order->status === 'completed')
                        <div id="ticket-details-{{ $order->id }}" class="grid transition-all duration-300 ease-in-out grid-rows-[0fr] opacity-0">
                            <div class="overflow-hidden">
                                <div class="px-5 pb-4">
                                    <h4 class="mb-4 text-sm font-semibold text-gray-300">Danh sách vé ({{ $order->tickets->count() }})</h4>
                            <div class="space-y-3">
                                @foreach($order->tickets as $ticket)
                                    <div class="flex items-center justify-between rounded-xl border border-white/5 bg-[#2a2a2a] p-3 transition hover:border-white/10">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-white tracking-widest">{{ $ticket->ticket_code }}</span>
                                            <span class="text-xs text-gray-400 mt-0.5">
                                                Vị trí: {{ $ticket->orderItem->ticketType->name }}
                                                @if($ticket->seat)
                                                    <span class="mx-1">•</span> Ghế {{ $ticket->seat->seat_number }}
                                                @endif
                                            </span>
                                            <span class="text-xs font-medium text-green-500 mt-1">{{ number_format($ticket->orderItem->price, 0, ',', '.') }} ₫</span>
                                        </div>
                                        <div class="flex gap-2">
                                            <a href="{{ route('user.tickets.show', $ticket->ticket_code) }}"
                                                class="flex gap-1 items-center rounded-lg bg-white/5 p-2 text-gray-400 hover:bg-white/10 hover:text-green-400 transition">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/><circle cx="12" cy="12" r="3"/></svg>
                                                <span>Xem chi tiết</span>
                                            </a>
                                            <button onclick="navigator.clipboard.writeText('{{ $ticket->ticket_code }}'); window.showAppToast('success', 'Thành công', 'Đã copy mã vé');" 
                                                class="rounded-lg bg-white/5 p-2 text-gray-400 hover:bg-white/10 hover:text-white transition" title="Copy mã vé">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                            </button>
                                            <button onclick="navigator.clipboard.writeText('{{ url('/') }}/tickets/{{ $ticket->ticket_code }}'); window.showAppToast('success', 'Thành công', 'Đã copy link chia sẻ');" 
                                                class="rounded-lg bg-white/5 p-2 text-gray-400 hover:bg-white/10 hover:text-blue-400 transition" title="Chia sẻ">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

                @empty
                    <div
                        class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-600 bg-[#2b2b2b] p-12 text-center shadow-sm">
                        <svg class="h-8 w-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                        </svg>
                        <p class="mt-4 text-sm text-gray-300 max-w-sm leading-relaxed">
                            @if($status)
                                Không có vé nào ở trạng thái này.
                            @else
                                Bạn chưa mua vé nào. Hãy khám phá các sự kiện hấp dẫn đang diễn ra nhé!
                            @endif
                        </p>
                        @if(!$status)
                            <a href="{{ route('home') }}"
                                class="mt-4 rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 transition">
                                Khám phá sự kiện
                            </a>
                        @else
                            <a href="{{ route('user.tickets') }}"
                                class="mt-4 text-sm font-semibold text-blue-600 hover:text-blue-700 transition">
                                Xem tất cả vé
                            </a>
                        @endif
                    </div>
                @endforelse

                @if($orders->hasPages())
                    <div class="mt-6">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection