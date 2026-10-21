{{-- Dropdown danh sách chi tiết các vé (Hỗ trợ cả Completed và Pending) --}}
<div id="ticket-details-{{ $order->id }}"
    class="grid transition-all duration-300 ease-in-out grid-rows-[0fr] opacity-0">
    <div class="overflow-hidden">
        <div class="px-4 pb-4 sm:px-5 sm:pb-5">
            <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                <h4 class="text-sm font-semibold text-gray-300">
                    @if($order->status === 'completed')
                        Danh sách vé ({{ $order->tickets->count() }})
                    @else
                        Danh sách vé đang giữ chỗ ({{ $order->tickets->count() }})
                    @endif
                </h4>
                @if(in_array($order->status, ['pending', 'processing']))
                    <span
                        class="inline-flex items-center gap-1.5 text-[11px] sm:text-xs text-amber-400 font-medium bg-amber-500/10 px-2.5 py-1 rounded-md border border-amber-500/20">
                        <x-icons.info class="h-3.5 w-3.5 shrink-0" />
                        Vé sẽ kích hoạt sau khi thanh toán thành công
                    </span>
                @endif
            </div>
            <div class="space-y-3">
                @foreach($order->tickets as $ticket)
                    @if($order->status === 'completed')
                        {{-- Vé trạng thái Completed (có các nút Xem chi tiết, Copy, Chia sẻ) --}}
                        <div
                            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 rounded-xl border border-white/5 bg-[#2a2a2a] p-3.5 transition hover:border-white/10">
                            @php
                                $ticketIsUsed = $ticket->is_used || !empty($ticket->checked_in_at);
                                $ticketCheckInTime = $ticket->checked_in_at ?? $ticket->used_at;
                                $ticketIsExpired = !$ticketIsUsed && (($isEventEnded ?? false) || in_array($order->status, ['expired', 'cancelled']));
                            @endphp
                            <!-- Thông tin vé -->
                            <div class="flex flex-col min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span
                                        class="font-mono text-sm font-bold text-white tracking-wider truncate">{{ $ticket->ticket_code }}</span>
                                    @if($ticketIsUsed)
                                        <span class="inline-flex items-center gap-1 rounded bg-red-500/10 px-2 py-0.5 text-[10px] font-semibold text-red-400 ring-1 ring-inset ring-red-500/20">
                                            Đã sử dụng
                                        </span>
                                    @elseif($ticketIsExpired)
                                        <span class="inline-flex items-center gap-1 rounded bg-rose-500/10 px-2 py-0.5 text-[10px] font-semibold text-rose-400 ring-1 ring-inset ring-rose-500/20">
                                            Hết hạn
                                        </span>
                                    @endif
                                </div>
                                <span class="text-xs text-gray-400 mt-0.5 truncate">
                                    Vị trí: {{ $ticket->orderItem->ticketType->name }}
                                    @if($ticket->seat)
                                        <span class="mx-1 text-gray-500">•</span> Ghế
                                        {{ $ticket->seat->seat_number }}
                                    @endif
                                </span>
                                <span
                                    class="text-xs font-semibold text-green-500 mt-1">{{ number_format($ticket->orderItem->price, 0, ',', '.') }}
                                    ₫</span>
                            </div>

                            <!-- Các nút thao tác vé (Responsive cho mobile & desktop) -->
                            <div
                                class="flex items-center gap-2 border-t border-white/5 pt-2.5 sm:border-0 sm:pt-0 shrink-0">
                                <a href="{{ route('user.tickets.show', $ticket->ticket_code) }}"
                                    class="flex flex-1 sm:flex-initial items-center justify-center gap-1.5 rounded-lg bg-white/5 px-3 py-2 text-xs font-medium text-gray-300 hover:bg-white/10 hover:text-green-400 transition whitespace-nowrap">
                                    <x-icons.eye class="h-4 w-4 shrink-0" />
                                    <span class="whitespace-nowrap">Xem chi tiết</span>
                                </a>
                                <button
                                    onclick="navigator.clipboard.writeText('{{ $ticket->ticket_code }}'); window.showAppToast('success', 'Thành công', 'Đã copy mã vé');"
                                    class="flex items-center justify-center rounded-lg bg-white/5 p-2 text-gray-400 hover:bg-white/10 hover:text-white transition shrink-0 cursor-pointer"
                                    title="Copy mã vé">
                                    <x-icons.copy class="h-4 w-4 shrink-0" />
                                </button>
                                <button
                                    onclick="navigator.clipboard.writeText('{{ url('/') }}/tickets/{{ $ticket->ticket_code }}'); window.showAppToast('success', 'Thành công', 'Đã copy link chia sẻ');"
                                    class="flex items-center justify-center rounded-lg bg-white/5 p-2 text-gray-400 hover:bg-white/10 hover:text-blue-400 transition shrink-0 cursor-pointer"
                                    title="Chia sẻ">
                                    <x-icons.share class="h-4 w-4 shrink-0" />
                                </button>
                            </div>
                        </div>
                    @else
                        {{-- Vé trạng thái Pending (không có nút xem chi tiết, sao chép, chia sẻ - thiết kế cân đối, chuyên nghiệp & responsive) --}}
                        <div
                            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 rounded-xl border border-white/5 bg-[#2a2a2a] p-3.5 transition hover:border-amber-500/20">
                            <!-- Thông tin vé -->
                            <div class="flex flex-col min-w-0">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="font-mono text-sm font-bold text-gray-200 tracking-wider truncate">{{ $ticket->ticket_code }}</span>
                                    <span
                                        class="text-[10px] uppercase font-semibold tracking-wider px-2 py-0.5 rounded bg-white/5 text-gray-400 border border-white/5">Chờ
                                        kích hoạt</span>
                                </div>
                                <div
                                    class="flex flex-wrap items-center gap-x-2 gap-y-0.5 text-xs text-gray-400 mt-1">
                                    <span>Vị trí: <strong
                                            class="text-gray-300 font-medium">{{ $ticket->orderItem->ticketType->name }}</strong></span>
                                    @if($ticket->seat)
                                        <span class="text-gray-500">•</span>
                                        <span>Ghế: <strong
                                                class="text-gray-300 font-medium">{{ $ticket->seat->seat_number }}</strong></span>
                                    @endif
                                </div>
                            </div>

                            <!-- Trạng thái giữ chỗ và giá vé thay cho các nút chức năng -->
                            <div
                                class="flex items-center justify-between sm:justify-end gap-3 border-t border-white/5 pt-2.5 sm:border-0 sm:pt-0 shrink-0">
                                <span
                                    class="text-xs font-bold text-green-400">{{ number_format($ticket->orderItem->price, 0, ',', '.') }}
                                    ₫</span>
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-amber-500/10 px-3 py-1.5 text-xs font-semibold text-amber-400 ring-1 ring-inset ring-amber-500/20">
                                    <x-icons.clock class="h-3.5 w-3.5 shrink-0 text-amber-400 animate-pulse" />
                                    Đang giữ chỗ
                                </span>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>
