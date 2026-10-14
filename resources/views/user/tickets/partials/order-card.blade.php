{{-- Thẻ đơn hàng --}}
<div id="order-{{ $order->id }}"
    class="scroll-mt-24 overflow-hidden rounded-2xl border border-dashed border-white/10 bg-[#292929] shadow-sm transition-all duration-300 hover:border-white/20 hover:shadow-md target:border-blue-500 target:ring-2 target:ring-blue-500/40">

    <!-- Tiêu đề thẻ đơn hàng -->
    <div
        class="flex items-start justify-between gap-3 sm:gap-4 border-b border-dashed border-white/10 px-4 py-3.5 sm:px-5 sm:py-4">

        <div class="min-w-0 flex-1">
            <p class="mb-1 text-xs font-medium uppercase tracking-wide text-gray-300/50">
                Sự kiện
            </p>

            <h3 class="truncate text-base sm:text-lg font-bold text-white">
                <a href="{{ route('events.show', $order->event->slug) }}"
                    class="transition hover:text-green-500">
                    {{ $order->event->name }}
                </a>
            </h3>
        </div>

        <!-- Trạng thái đơn hàng -->
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
        @elseif($order->status === 'processing')
            <div class="flex flex-col items-end gap-1 shrink-0">
                <span
                    class="inline-flex shrink-0 items-center gap-1.5 rounded-full
                           bg-blue-500/10 px-3 py-1.5 text-xs font-semibold
                           text-blue-400 ring-1 ring-inset ring-blue-500/20">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Chờ duyệt thanh toán
                </span>
            </div>
        @elseif($order->status === 'pending')
            <!-- Trạng thái Chờ xử lý & thời gian hết hạn trên header -->
            <div class="flex flex-col items-end gap-1 shrink-0">
                <span
                    class="inline-flex shrink-0 items-center gap-1.5 rounded-full
                           bg-amber-500/10 px-3 py-1.5 text-xs font-semibold
                           text-amber-400 ring-1 ring-inset ring-amber-500/20">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Chờ thanh toán
                </span>
            </div>
        @elseif($order->status === 'expired')
            <div class="flex flex-col items-end gap-1 shrink-0">
                <span
                    class="inline-flex shrink-0 items-center gap-1.5 rounded-full
                           bg-gray-500/10 px-3 py-1.5 text-xs font-semibold
                           text-gray-400 ring-1 ring-inset ring-gray-500/20">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Hết hạn giữ vé
                </span>
            </div>
        @else
            <div class="flex flex-col items-end gap-1 shrink-0">
                <span
                    class="inline-flex shrink-0 items-center gap-1.5 rounded-full
                           bg-red-500/10 px-3 py-1.5 text-xs font-semibold
                           text-red-400 ring-1 ring-inset ring-red-500/20">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Đã hủy
                </span>
            </div>
        @endif

    </div>

    <!-- Nội dung chi tiết đơn hàng -->
    <div class="px-4 py-3.5 sm:px-5 sm:py-4">

        <!-- Thông tin đơn hàng (Lưới responsive) -->
        <div class="grid grid-cols-2 gap-3.5 sm:grid-cols-4 sm:gap-4">

            <!-- Mã đơn hàng -->
            <div>
                <p class="mb-1 text-xs text-gray-300/50">
                    Mã đơn hàng
                </p>

                <p class="font-mono text-sm font-semibold text-gray-200 flex items-center gap-2">
                    <span class="truncate">{{ $order->order_code }}</span>
                    <svg onclick="navigator.clipboard.writeText('{{ $order->order_code }}'); window.showAppToast('success', 'Thành công', 'Đã copy mã đơn hàng');"
                        class="h-4 w-4 text-gray-300/50 hover:text-white cursor-pointer shrink-0"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </p>
            </div>

            <!-- Thời gian sự kiện -->
            <div>
                <p class="mb-1 text-xs text-gray-300/50">
                    Thời gian sự kiện
                </p>

                <div class="flex items-center gap-1.5 text-sm text-gray-200">
                    <svg class="h-4 w-4 text-gray-300/50 shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="truncate">{{ $order->event->starts_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>

            <!-- Số lượng vé & Nút sửa vé cho đơn Pending -->
            <div>
                <p class="mb-1 text-xs text-gray-300/50">
                    Số lượng
                </p>

                <div class="flex items-center flex-wrap gap-2 text-sm text-gray-200">
                    <div class="flex items-center gap-1.5">
                        <svg class="h-4 w-4 text-gray-300/50 shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                        </svg>
                        <span>{{ $order->items->sum('quantity') }} vé</span>
                    </div>

                    @if($order->status === 'pending' && $order->expires_at > now())
                        <button type="button" onclick="openEditTicketModal({{ $order->id }})"
                            class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-semibold text-blue-400 hover:text-blue-300 transition cursor-pointer"
                            title="Sửa số lượng vé">
                            <span>[Sửa vé]</span>
                        </button>
                    @endif
                </div>
            </div>

            <!-- Tổng tiền -->
            <div>
                <p class="mb-1 text-xs text-gray-300/50">
                    Tổng tiền
                </p>

                <p class="text-sm font-bold text-green-500">
                    {{ number_format($order->total_amount, 0, ',', '.') }} ₫
                </p>
            </div>

        </div>

        <!-- Cảnh báo thời gian hết hạn cho đơn Chờ thanh toán / Chờ duyệt -->
        @if(in_array($order->status, ['pending', 'processing']) && $order->expires_at)
            <div
                class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5 rounded-xl border border-amber-500/20 bg-amber-500/10 px-3.5 py-2.5 text-xs text-amber-300">
                <div class="flex items-center gap-2">
                    <svg class="h-4 w-4 shrink-0 text-amber-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>
                        Thời hạn thanh toán: <strong
                            class="font-semibold text-amber-200">{{ $order->expires_at->format('H:i - d/m/Y') }}</strong>
                    </span>
                </div>
                <div>
                    @if(now() < $order->expires_at)
                        <span data-order-countdown="{{ $order->expires_at->toIso8601String() }}"
                            class="inline-flex items-center gap-1 font-medium text-amber-400 bg-amber-500/20 px-2.5 py-1 rounded-full text-[11px] sm:text-xs">
                            Còn lại:
                            {{ $order->expires_at->locale('vi')->diffForHumans(now(), ['syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE, 'parts' => 2]) }}
                        </span>
                    @else
                        <span
                            class="inline-flex items-center gap-1 font-semibold text-red-400 bg-red-500/20 px-2.5 py-1 rounded-full text-[11px] sm:text-xs">
                            Đã quá hạn giữ vé
                        </span>
                    @endif
                </div>
            </div>
        @endif

        <!-- Lý do hết hạn nếu đơn hết hạn -->
        @if($order->status === 'expired')
            <div
                class="mt-4 flex items-center gap-2.5 rounded-xl border border-gray-500/20 bg-gray-500/10 px-3.5 py-2.5 text-xs text-gray-300">
                <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Trạng thái: <strong class="font-semibold text-gray-200">{{ $order->cancel_reason ?: 'Hết hạn giữ vé thanh toán' }}</strong></span>
            </div>
        @endif

        <!-- Lý do hủy đơn hàng nếu đơn bị hủy -->
        @if($order->status === 'cancelled' && $order->cancel_reason)
            <div
                class="mt-4 flex items-center gap-2.5 rounded-xl border border-red-500/20 bg-red-500/10 px-3.5 py-2.5 text-xs text-red-300">
                <svg class="h-4 w-4 shrink-0 text-red-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>Lý do: <strong
                        class="font-semibold text-red-200">{{ $order->cancel_reason }}</strong></span>
            </div>
        @endif

        <!-- Lý do nếu không có cancel_reason -->
        @if($order->status === 'cancelled' && !$order->cancel_reason)
            <div
                class="mt-4 flex items-center gap-2.5 rounded-xl border border-red-500/20 bg-red-500/10 px-3.5 py-2.5 text-xs text-red-300">
                <svg class="h-4 w-4 shrink-0 text-red-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>Lý do: <strong class="font-semibold text-red-200">Người dùng hủy đơn</strong></span>
            </div>
        @endif

        <!-- Các nút hành động -->
        <div
            class="mt-5 flex flex-wrap items-center justify-end gap-2 @if ($order->status !== 'cancelled' || $order->tickets->count() > 0) border-t border-dashed border-white/10 pt-4 @endif">

            <!-- Nút xem chi tiết vé (hỗ trợ cả Completed, Pending và Processing) -->
            @if($order->tickets->count() > 0 && ($order->status === 'completed' || (in_array($order->status, ['pending', 'processing']) && $order->expires_at > now())))
                <button type="button" onclick="toggleOrderTickets({{ $order->id }})"
                    class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-white transition cursor-pointer hover:text-blue-400">
                    <span id="btn-text-{{ $order->id }}">Xem chi tiết vé</span>
                    <svg id="btn-icon-{{ $order->id }}" class="h-4 w-4 transition-transform duration-200"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
            @endif

            @if(in_array($order->status, ['pending', 'processing']))
                @if ($order->expires_at < now())
                    <a href="{{ route('events.book', $order->event->slug) }}"
                        class="rounded-lg border border-emerald-500/20 bg-emerald-500/10 px-4 py-2 text-xs font-semibold text-emerald-400 transition hover:bg-emerald-500/20">
                        Đặt lại vé
                    </a>
                @else
                    @if($order->status === 'processing' || $order->payment_method === 'direct')
                        <a href="{{ route('orders.instruction', $order) }}"
                            class="rounded-lg border border-blue-500/20 bg-blue-500/10 px-4 py-2 text-xs font-semibold text-blue-400 transition hover:bg-blue-500/20">
                            Hướng dẫn thanh toán
                        </a>
                    @else
                        <button type="button" onclick="openCheckoutModal({{ $order->id }})"
                            class="rounded-lg bg-emerald-500 px-4 py-2 text-xs font-semibold text-white transition hover:bg-emerald-600 shadow-sm shadow-emerald-500/20 cursor-pointer">
                            Thanh toán ngay
                        </button>
                    @endif

                    <form action="{{ route('orders.cancel', $order) }}" method="POST"
                        onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này không? Vé đang giữ sẽ bị hủy bỏ.');">
                        @csrf
                        <button type="submit"
                            class="rounded-lg border border-white/10 bg-white/5 px-4 py-2 text-xs font-semibold text-gray-300 transition hover:border-red-500/30 hover:bg-red-500/10 hover:text-red-400 cursor-pointer">
                            Hủy đơn hàng
                        </button>
                    </form>
                @endif
            @elseif(in_array($order->status, ['cancelled', 'expired']))
                <a href="{{ route('events.book', $order->event->slug) }}"
                    class="rounded-lg border border-emerald-500/20 bg-emerald-500/10 px-4 py-2 text-xs font-semibold text-emerald-400 transition hover:bg-emerald-500/20">
                    Đặt lại vé
                </a>
            @endif

        </div>

    </div>

    <!-- Dropdown danh sách chi tiết các vé -->
    @if(($order->status === 'completed' || (in_array($order->status, ['pending', 'processing']) && $order->expires_at > now())) && $order->tickets->count() > 0)
        @include('user.tickets.partials.ticket-list', ['order' => $order])
    @endif

</div>
