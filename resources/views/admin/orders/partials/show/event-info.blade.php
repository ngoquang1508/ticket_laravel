{{-- Thông tin sự kiện --}}
<div class="rounded-xl border border-gray-200 bg-white p-6 shadow-2xs">
    <h3 class="font-bold text-gray-900 border-b pb-3 mb-4">Thông tin sự kiện</h3>
    @if ($order->event->image)
        <img src="{{ asset('storage/' . $order->event->image) }}" alt="{{ $order->event->name }}"
            class="mb-3 h-32 w-full rounded-lg object-cover border border-gray-100">
    @endif
    <a href="{{ route('admin.events.show', $order->event) }}"
        class="text-sm font-bold text-gray-900 hover:text-primary-600 hover:underline transition">
        {{ $order->event->name }}
    </a>
    <div class="mt-2 space-y-1.5 text-xs text-gray-600">
        <p class="flex items-center gap-1.5">
            <span class="text-gray-400">📍</span>
            <span>{{ $order->event->location?->name ?: 'N/A' }}</span>
        </p>
        <p class="flex items-center gap-1.5">
            <span class="text-gray-400">🕒</span>
            <span>{{ $order->event->starts_at?->format('d/m/Y H:i') }}</span>
        </p>
        <p class="flex items-center gap-1.5">
            <span class="text-gray-400">🎟️</span>
            <span>{{ $order->event->sale_mode === 'assigned_seat' ? 'Ghế ngồi chỉ định' : ($order->event->sale_mode === 'free_sale' ? 'Vé tự do' : 'Khu vực') }}</span>
        </p>
    </div>
</div>
