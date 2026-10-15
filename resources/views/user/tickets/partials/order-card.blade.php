@php
    $isEventEnded = $order->event?->ends_at?->isPast();

    $isExpired = $order->status === 'expired'
        || ($order->status === 'cancelled' && str_contains(strtolower($order->cancel_reason ?? ''), 'hết hạn'))
        || ($order->status === 'completed' && $isEventEnded);

    $statusConfig = match (true) {
        $isExpired => [
            'text' => 'Hết hạn',
            'color' => 'rose',
            'icon' => 'clock',
        ],
        $order->status === 'completed' => [
            'text' => 'Thành công',
            'color' => 'emerald',
            'icon' => 'check',
        ],
        $order->status === 'processing' => [
            'text' => 'Chờ duyệt thanh toán',
            'color' => 'blue',
            'icon' => 'clock',
        ],
        $order->status === 'pending' => [
            'text' => 'Chờ thanh toán',
            'color' => 'amber',
            'icon' => 'clock',
        ],
        default => [
            'text' => 'Đã hủy',
            'color' => 'red',
            'icon' => 'x',
        ],
    };
@endphp

<div id="order-{{ $order->id }}"
    class="scroll-mt-24 overflow-hidden rounded-2xl border border-dashed border-white/10 bg-[#292929] shadow-sm transition hover:border-white/20 hover:shadow-md target:border-blue-500 target:ring-2 target:ring-blue-500/40">

    {{-- Header --}}
    <div
        class="flex items-start justify-between gap-3 border-b border-dashed border-white/10 px-4 py-3.5 sm:px-5 sm:py-4">

        <div class="min-w-0 flex-1">
            <p class="mb-1 text-xs font-medium uppercase tracking-wide text-gray-300/50">
                Sự kiện
            </p>

            <h3 class="truncate text-base font-bold text-white sm:text-lg">
                <a href="{{ route('events.show', $order->event->slug) }}" class="transition hover:text-green-500">
                    {{ $order->event->name }}
                </a>
            </h3>
        </div>

        {{-- Trạng thái --}}
        <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full
            bg-{{ $statusConfig['color'] }}-500/10
            px-3 py-1.5 text-xs font-semibold
            text-{{ $statusConfig['color'] }}-400
            ring-1 ring-inset ring-{{ $statusConfig['color'] }}-500/20">

            @if($statusConfig['icon'] === 'check')
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
            @elseif($statusConfig['icon'] === 'x')
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            @else
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            @endif

            {{ $statusConfig['text'] }}
        </span>
    </div>

    {{-- Thông tin đơn --}}
    <div class="px-4 py-3.5 sm:px-5 sm:py-4">

        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">

            {{-- Mã đơn --}}
            <div>
                <p class="mb-1 text-xs text-gray-300/50">Mã đơn hàng</p>

                <button type="button"
                    onclick="navigator.clipboard.writeText('{{ $order->order_code }}'); window.showAppToast('success', 'Thành công', 'Đã copy mã đơn hàng');"
                    class="flex items-center gap-2 font-mono text-sm font-semibold text-gray-200 hover:text-white">

                    <span class="truncate">{{ $order->order_code }}</span>

                    <svg class="h-4 w-4 shrink-0 text-gray-300/50" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </button>
            </div>

            {{-- Thời gian --}}
            <div>
                <p class="mb-1 text-xs text-gray-300/50">Thời gian sự kiện</p>

                <p class="flex items-center gap-1.5 text-sm text-gray-200">
                    <span>📅</span>
                    {{ $order->event->starts_at->format('d/m/Y H:i') }}
                </p>
            </div>

            {{-- Số lượng --}}
            <div>
                <p class="mb-1 text-xs text-gray-300/50">Số lượng</p>

                <div class="flex items-center gap-2 text-sm text-gray-200">
                    <span>{{ $order->items->sum('quantity') }} vé</span>

                    @if($order->status === 'pending' && $order->expires_at?->isFuture())
                        <button type="button" onclick="openEditTicketModal({{ $order->id }})"
                            class="text-xs font-semibold text-blue-400 hover:text-blue-300">
                            [Sửa vé]
                        </button>
                    @endif
                </div>
            </div>

            {{-- Tổng tiền --}}
            <div>
                <p class="mb-1 text-xs text-gray-300/50">Tổng tiền</p>

                <p class="text-sm font-bold text-green-500">
                    {{ number_format($order->total_amount, 0, ',', '.') }} ₫
                </p>
            </div>
        </div>

        {{-- Thời hạn --}}
        @if(in_array($order->status, ['pending', 'processing']) && $order->expires_at)
            <div class="mt-4 flex items-center justify-between gap-3 rounded-xl
                border border-amber-500/20 bg-amber-500/10 px-3.5 py-2.5
                text-xs text-amber-300">

                <span>
                    Thời hạn thanh toán:
                    <strong class="text-amber-200">
                        {{ $order->expires_at->format('H:i - d/m/Y') }}
                    </strong>
                </span>

                @if($order->expires_at->isFuture())
                        <span data-order-countdown="{{ $order->expires_at->toIso8601String() }}"
                            class="rounded-full bg-amber-500/20 px-2.5 py-1 text-amber-400">
                            Còn lại:
                            {{ $order->expires_at->locale('vi')->diffForHumans(now(), [
                        'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE,
                        'parts' => 2
                    ]) }}
                        </span>
                @else
                    <span class="rounded-full bg-red-500/20 px-2.5 py-1 text-red-400">
                        Đã quá hạn
                    </span>
                @endif
            </div>
        @endif

        {{-- Thông báo --}}
        @if($isExpired)
            <div class="mt-4 rounded-xl border border-rose-500/20 bg-rose-500/10
                px-3.5 py-2.5 text-xs text-rose-300">

                {{ $order->cancel_reason ?: ($isEventEnded
            ? 'Sự kiện đã kết thúc'
            : 'Hết hạn giữ vé thanh toán') }}
            </div>

        @elseif($order->status === 'cancelled')
            <div class="mt-4 rounded-xl border border-red-500/20 bg-red-500/10
                px-3.5 py-2.5 text-xs text-red-300">

                Lý do:
                <strong class="text-red-200">
                    {{ $order->cancel_reason ?: 'Người dùng hủy đơn' }}
                </strong>
            </div>
        @endif

        {{-- Actions --}}
        <div class="mt-5 flex flex-wrap items-center justify-end gap-2
            border-t border-dashed border-white/10 pt-4 text-xs font-semibold">

            {{-- Xem vé --}}
            @if(
                    $order->tickets->count() &&
                    ($order->status === 'completed' ||
                        (in_array($order->status, ['pending', 'processing']) && $order->expires_at?->isFuture()))
                )

                <button type="button" onclick="toggleOrderTickets({{ $order->id }})"
                    class="flex items-center gap-2 px-3 py-2 text-white hover:text-blue-400">

                    <span id="btn-text-{{ $order->id }}">
                        Xem chi tiết vé
                    </span>

                    <svg id="btn-icon-{{ $order->id }}" class="h-4 w-4 transition-transform" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
            @endif

            {{-- Pending / Processing --}}
            @if(in_array($order->status, ['pending', 'processing']))

                @if($order->expires_at?->isPast())

                    <a href="{{ route('events.book', $order->event->slug) }}" class="btn-action">
                        Đặt lại vé
                    </a>

                @else

                    @if($order->status === 'processing' || $order->payment_method === 'direct')
                        <a href="{{ route('orders.instruction', $order) }}"
                            class="btn-action px-4 py-2 rounded-lg border border-green-400 text-green-400 hover:bg-green-400 hover:text-white transition-colors duration-300">
                            Hướng dẫn thanh toán
                        </a>
                    @else
                        <button type="button" onclick="openCheckoutModal({{ $order->id }})"
                            class="rounded-lg bg-emerald-500 px-4 py-2 text-white hover:bg-emerald-600">
                            Thanh toán ngay
                        </button>
                    @endif

                    <form action="{{ route('orders.cancel', $order) }}" method="POST"
                        onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?');">
                        @csrf

                        <button type="submit"
                            class="rounded-lg border border-white/10 bg-white/5 px-4 py-2 text-gray-300 hover:text-red-400 cursor-pointer">
                            Hủy đơn hàng
                        </button>
                    </form>

                @endif

                {{-- Cancelled / Expired --}}
            @elseif(in_array($order->status, ['cancelled', 'expired']))

                <a href="{{ route('events.book', $order->event->slug) }}" class="rounded-lg border border-emerald-500/20 bg-emerald-500/10
                    px-4 py-2 text-emerald-400 hover:bg-emerald-500/20">
                    Đặt lại vé
                </a>

            @endif
        </div>
    </div>

    {{-- Danh sách vé --}}
    @if(
            $order->tickets->count() &&
            ($order->status === 'completed' ||
                (in_array($order->status, ['pending', 'processing']) && $order->expires_at?->isFuture()))
        )

        @include('user.tickets.partials.ticket-list', [
            'order' => $order,
            'isEventEnded' => $isEventEnded
        ])
    @endif

</div>