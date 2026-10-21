{{-- Danh sách từng vé phát hành (Tickets) --}}
<div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-2xs">
    <div class="flex items-center justify-between border-b border-gray-200 bg-gray-50/80 px-6 py-4">
        <div>
            <h2 class="font-bold text-gray-900">Danh sách vé điện tử</h2>
            <p class="text-xs text-gray-500">Tổng cộng: {{ $order->tickets->count() }} mã vé được cấp phát</p>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-gray-100 bg-white text-xs font-semibold uppercase text-gray-500">
                <tr>
                    <th class="px-6 py-3.5">Mã vé</th>
                    <th class="px-6 py-3.5">Loại vé</th>
                    <th class="px-6 py-3.5">Vị trí ghế</th>
                    @if($order->status === 'completed')
                        <th class="px-6 py-3.5">Check-in</th>
                        <th class="px-6 py-3.5 text-right">Xem vé</th>
                    @else
                        <th class="px-6 py-3.5">Trạng thái</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($order->tickets as $ticket)
                    <tr class="hover:bg-gray-50/80 transition-colors">
                        {{-- Mã vé --}}
                        <td class="px-6 py-3.5 font-mono text-xs font-bold text-gray-900">
                            {{ $ticket->ticket_code }}
                        </td>

                        {{-- Loại vé --}}
                        <td class="px-6 py-3.5 text-gray-700">
                            {{ $ticket->orderItem?->ticketType?->name ?: '-' }}
                        </td>

                        {{-- Vị trí ghế --}}
                        <td class="px-6 py-3.5">
                            @if ($ticket->seat)
                                <span class="inline-flex items-center gap-1 rounded bg-indigo-50 px-2 py-0.5 text-xs font-semibold text-indigo-700 border border-indigo-200">
                                    Hàng {{ $ticket->seat->row_label }} - Ghế {{ $ticket->seat->seat_number }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400">Vé tự do (Free sale)</span>
                            @endif
                        </td>
                        @if($order->status === 'completed')
                            {{-- Trạng thái check-in --}}
                            <td class="px-6 py-3.5">
                                @if ($ticket->is_used)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-[11px] font-semibold text-emerald-800">
                                        ✓ Đã sử dụng ({{ $ticket->used_at?->format('d/m H:i') }})
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-[11px] font-medium text-gray-600">
                                        Chưa check-in
                                    </span>
                                @endif
                            </td>

                            {{-- Nút xem chi tiết vé điện tử --}}
                            <td class="px-6 py-3.5 text-right">
                                <a href="{{ route('tickets.public.show', $ticket->ticket_code) }}"
                                    target="_blank"
                                    class="rounded-lg border border-gray-200 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 hover:text-primary-600 transition inline-flex items-center gap-1 shadow-2xs"
                                    title="Mở vé điện tử">
                                    <x-icons.eye />
                                    <span>Vé</span>
                                </a>
                            </td>
                        @else
                            {{-- Trạng thái đơn hàng khi chưa hoàn tất --}}
                            <td class="px-6 py-3.5">
                                <x-order-status-badge :status="$order->status" />
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $order->status === 'completed' ? 5 : 4 }}" class="px-6 py-8 text-center text-xs text-gray-500">
                            Chưa có vé nào được tạo cho đơn hàng này.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
