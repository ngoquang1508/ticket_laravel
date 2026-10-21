{{-- ========================================================= --}}
{{-- PARTIAL: TAB VÉ CỦA TÔI (DỮ LIỆU TỪ ORDERS & TICKETS)    --}}
{{-- ========================================================= --}}
<div class="space-y-5">

    {{-- Thống kê nhanh hoạt động vé --}}
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3 sm:gap-4">

        {{-- Thẻ: Vé sắp tới --}}
        <div class="rounded-2xl border border-white/10 bg-[#292929] p-4 sm:p-5 shadow-lg">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-gray-400">Vé sắp tới</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-xl border border-emerald-500/20 bg-emerald-500/10 text-emerald-400">
                    <x-icons.ticket class="h-4 w-4" />
                </div>
            </div>
            <p class="mt-2 text-xl sm:text-2xl font-semibold text-white">
                {{ number_format($upcomingTicketsCount) }}
            </p>
            <p class="mt-0.5 text-[11px] text-emerald-400 flex items-center gap-1">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                Vé sự kiện còn hạn
            </p>
        </div>

        {{-- Thẻ: Tổng số vé --}}
        <div class="rounded-2xl border border-white/10 bg-[#292929] p-4 sm:p-5 shadow-lg">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-gray-400">Tổng vé đã đặt</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-xl border border-blue-500/20 bg-blue-500/10 text-blue-400">
                    <x-icons.document-text class="h-4 w-4" />
                </div>
            </div>
            <p class="mt-2 text-xl sm:text-2xl font-semibold text-white">
                {{ number_format($totalTickets) }}
            </p>
            <p class="mt-0.5 text-[11px] text-gray-400">
                Vé đặt thành công
            </p>
        </div>

        {{-- Thẻ: Tổng số đơn hàng --}}
        <div class="rounded-2xl border border-white/10 bg-[#292929] p-4 sm:p-5 shadow-lg">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-gray-400">Tổng đơn hàng</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-xl border border-purple-500/20 bg-purple-500/10 text-purple-400">
                    <x-icons.shopping-bag class="h-4 w-4" />
                </div>
            </div>
            <p class="mt-2 text-xl sm:text-2xl font-semibold text-white">
                {{ number_format($totalOrders) }}
            </p>
            <p class="mt-0.5 text-[11px] text-gray-400">
                Tất cả đơn
            </p>
        </div>

    </div>

    {{-- Khung danh sách vé và đơn hàng gần nhất --}}
    <div class="overflow-hidden rounded-2xl border border-white/10 bg-[#292929] shadow-xl">

        {{-- Header của danh sách --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-white/10 px-5 py-4 sm:px-7 sm:py-5">
            <div>
                <h2 class="text-base sm:text-lg font-bold text-white">
                    Vé đã đặt thành công
                </h2>
                <p class="mt-0.5 text-xs text-gray-400">
                    Danh sách các vé đặt thành công
                </p>
            </div>

            <a href="{{ route('user.tickets') }}"
                class="inline-flex items-center gap-1.5 self-start sm:self-auto rounded-xl border border-white/10 bg-[#222222] px-3.5 py-2 text-xs font-semibold text-emerald-400 hover:border-emerald-500/30 hover:bg-emerald-500/10 transition">
                <span>Trang quản lý vé</span>
                <x-icons.chevron-right class="h-3.5 w-3.5" />
            </a>
        </div>

        {{-- Danh sách đơn hàng & vé --}}
        <div class="p-4 sm:p-6 space-y-4">

            @forelse($orders as $order)
                @php
                    $event = $order->event;
                    $isCompleted = $order->status === 'completed';
                    $isPending = in_array($order->status, ['pending', 'processing']);
                    $isExpired = $order->status === 'expired' || ($isCompleted && $event && $event->ends_at < now());
                @endphp

                <div class="overflow-hidden rounded-xl border border-white/5 bg-[#222222] transition duration-200 hover:border-white/15">
                    
                    {{-- Tiêu đề đơn hàng --}}
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-white/5 bg-white/[0.02] px-4 py-3 sm:px-5">
                        
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-mono text-xs font-bold text-white">
                                #{{ $order->order_code }}
                            </span>
                            <span class="text-gray-500 text-xs">•</span>
                            <span class="text-xs text-gray-400">
                                {{ $order->created_at->format('H:i - d/m/Y') }}
                            </span>
                        </div>

                        {{-- Huy hiệu trạng thái đơn hàng --}}
                        <div>
                            @if($isExpired)
                                <span class="inline-flex items-center rounded-full border border-gray-500/30 bg-gray-500/10 px-2.5 py-0.5 text-[11px] font-semibold text-gray-400">
                                    Đã kết thúc
                                </span>
                            @elseif($isCompleted)
                                <span class="inline-flex items-center gap-1 rounded-full border border-emerald-500/30 bg-emerald-500/10 px-2.5 py-0.5 text-[11px] font-semibold text-emerald-400">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                                    Thành công
                                </span>
                            @elseif($isPending)
                                <span class="inline-flex items-center rounded-full border border-amber-500/30 bg-amber-500/10 px-2.5 py-0.5 text-[11px] font-semibold text-amber-400">
                                    Chờ thanh toán
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full border border-gray-500/30 bg-gray-500/10 px-2.5 py-0.5 text-[11px] font-semibold text-gray-400">
                                    Đã hủy
                                </span>
                            @endif
                        </div>

                    </div>

                    {{-- Thông tin sự kiện và các vé bên trong --}}
                    <div class="p-4 sm:p-5">
                        
                        {{-- Tên sự kiện --}}
                        <div class="flex items-start gap-3">
                            <div class="min-w-0 flex-1">
                                <h3 class="truncate text-sm sm:text-base font-bold text-white">
                                    {{ $event->name ?? 'Sự kiện không xác định' }}
                                </h3>
                                
                                @if($event)
                                    <div class="mt-1 flex flex-wrap items-center gap-2.5 text-xs text-gray-400">
                                        <span class="flex items-center gap-1">
                                            <x-icons.calendar class="h-3.5 w-3.5 text-gray-500" />
                                            {{ $event->starts_at->format('H:i - d/m/Y') }}
                                        </span>

                                        @if($event->starts_at >= now())
                                            <span class="inline-flex items-center gap-1 rounded bg-emerald-500/15 px-2 py-0.5 text-[10px] font-bold text-emerald-400">
                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                                Sắp diễn ra
                                            </span>
                                        @else
                                            <span class="rounded bg-white/5 px-2 py-0.5 text-[10px] font-medium text-gray-400">
                                                Đã diễn ra
                                            </span>
                                        @endif

                                        @if($event->location)
                                            <span class="flex items-center gap-1 truncate max-w-xs">
                                                <x-icons.location class="h-3.5 w-3.5 text-gray-500 shrink-0" />
                                                <span class="truncate">{{ $event->location->name ?? $event->location->address }}</span>
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <div class="text-right shrink-0">
                                <span class="text-xs text-gray-400">Tổng tiền</span>
                                <p class="text-sm sm:text-base font-semibold text-white">
                                    {{ number_format($order->total_amount, 0, ',', '.') }} ₫
                                </p>
                            </div>
                        </div>

                        {{-- Danh sách các vé điện tử thuộc đơn hàng --}}
                        @if($order->tickets->count() > 0)
                            <div class="mt-4 space-y-2 border-t border-white/5 pt-3">
                                <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">
                                    Danh sách mã vé ({{ $order->tickets->count() }} vé):
                                </span>

                                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                    @foreach($order->tickets as $ticket)
                                        @php
                                            $ticketExpired = $isExpired;
                                            $ticketUsed = $ticket->is_used || $ticket->checked_in_at;
                                        @endphp

                                        <div class="flex items-center justify-between rounded-lg border border-white/5 bg-[#292929] px-3 py-2 text-xs">
                                            
                                            <div class="min-w-0 pr-2">
                                                <div class="flex items-center gap-1.5">
                                                    <span class="font-mono font-bold text-gray-200 truncate">
                                                        {{ $ticket->ticket_code }}
                                                    </span>

                                                    @if($ticketUsed)
                                                        <span class="rounded bg-gray-700 px-1.5 py-0.2 text-[10px] text-gray-300">Đã dùng</span>
                                                    @elseif($ticketExpired)
                                                        <span class="rounded bg-gray-600/30 px-1.5 py-0.2 text-[10px] text-gray-400">Hết hạn</span>
                                                    @else
                                                        <span class="rounded bg-emerald-500/20 px-1.5 py-0.2 text-[10px] text-emerald-300 font-semibold">Khả dụng</span>
                                                    @endif
                                                </div>

                                                <div class="mt-0.5 text-[11px] text-gray-400 truncate">
                                                    <span>{{ $ticket->orderItem->ticketType->name ?? 'Vé tham dự' }}</span>
                                                    @if($ticket->seat)
                                                        <span>• Ghế: <strong class="text-gray-300">{{ $ticket->seat->seat_number }}</strong></span>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Nút xem mã QR của vé --}}
                                            <a href="{{ route('user.tickets.show', $ticket->ticket_code) }}"
                                                class="shrink-0 rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-2.5 py-1 text-[11px] font-semibold text-emerald-400 hover:bg-emerald-500 hover:text-gray-950 transition">
                                                Xem QR
                                            </a>

                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                    </div>

                </div>
            @empty
                {{-- Khi chưa có vé completed nào --}}
                <div class="rounded-xl border border-dashed border-white/10 bg-[#222222] p-8 text-center sm:p-12">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-gray-400">
                        <x-icons.ticket class="h-7 w-7" />
                    </div>

                    <h3 class="mt-4 text-sm sm:text-base font-bold text-white">
                        Bạn chưa có vé đã đặt thành công nào
                    </h3>
                    <p class="mx-auto mt-1 max-w-sm text-xs text-gray-400 leading-relaxed">
                        Hãy khám phá các sự kiện âm nhạc, thể thao và hội thảo hấp dẫn ngay trên hệ thống.
                    </p>

                    <div class="mt-5">
                        <a href="{{ route('events.index') }}"
                            class="inline-flex items-center gap-2 rounded-xl bg-emerald-500 px-5 py-2.5 text-xs sm:text-sm font-bold text-gray-950 transition hover:bg-emerald-400 hover:shadow-lg hover:shadow-emerald-500/25">
                            <span>Khám phá sự kiện ngay</span>
                            <x-icons.arrow-right class="h-4 w-4" />
                        </a>
                    </div>
                </div>
            @endforelse

            {{-- Phân trang nếu có nhiều trang --}}
            @if($orders->hasPages())
                <div class="pt-3">
                    {{ $orders->fragment('tickets')->links() }}
                </div>
            @endif

        </div>

    </div>

</div>
