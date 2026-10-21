{{-- Modal Sửa số lượng vé cho đơn hàng Pending --}}
<div id="edit-ticket-modal-{{ $order->id }}"
    onclick="if(event.target === this) closeEditTicketModal({{ $order->id }})"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75 backdrop-blur-sm hidden transition-all duration-200">
    <div
        class="relative w-full max-w-lg rounded-2xl border border-white/10 bg-[#222222] p-5 sm:p-6 shadow-2xl text-left transform transition-transform duration-200">
        <!-- Header modal -->
        <div class="flex items-center justify-between border-b border-white/10 pb-4">
            <div>
                <h3 class="text-base sm:text-lg font-bold text-white">Chỉnh sửa số lượng vé</h3>
                <p class="text-xs text-gray-400 mt-0.5 font-mono">Đơn hàng #{{ $order->order_code }}</p>
            </div>
            <button type="button" onclick="closeEditTicketModal({{ $order->id }})"
                class="rounded-lg p-1.5 text-gray-400 hover:bg-white/10 hover:text-white transition cursor-pointer">
                <x-icons.close class="h-5 w-5" />
            </button>
        </div>

        @if($order->event->sale_mode === 'assigned_seat')
            <!-- Sự kiện chọn ghế ngồi trực tiếp -->
            <div class="py-5 space-y-4">
                <div
                    class="flex items-start gap-3 rounded-xl border border-blue-500/20 bg-blue-500/10 p-3.5 text-xs text-blue-300">
                    <x-icons.info class="h-5 w-5 shrink-0 text-blue-400 mt-0.5" />
                    <div>
                        <p class="font-semibold text-blue-200 mb-1">Chỉnh sửa ghế trên sơ đồ trực tiếp</p>
                        <p class="text-blue-300/90 leading-relaxed">Sự kiện áp dụng sơ đồ chọn ghế. Bạn có thể
                            mở sơ đồ ghế để xem vị trí các ghế đang giữ, chọn thêm ghế trống hoặc đổi sang vị
                            trí ghế khác.</p>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 border-t border-white/10 pt-4">
                <button type="button" onclick="closeEditTicketModal({{ $order->id }})"
                    class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-xs font-semibold text-gray-300 hover:bg-white/10 transition cursor-pointer">
                    Đóng
                </button>
                <a href="{{ route('orders.edit-tickets', array_filter(['order' => $order->id, 'status' => request('status', $order->status), 'page' => request('page')])) }}"
                    class="rounded-xl bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-500 transition">
                    Chỉnh sửa ghế trên sơ đồ
                </a>
            </div>
        @else
            <!-- Sự kiện vé tự do (General admission / Free sale) -->
            <form action="{{ route('orders.update-tickets', $order) }}" method="POST"
                id="edit-ticket-form-{{ $order->id }}">
                @csrf
                <input type="hidden" name="status" value="{{ request('status', $order->status) }}">
                @if(request('page'))
                    <input type="hidden" name="page" value="{{ request('page') }}">
                @endif
                <div class="py-4 space-y-4 max-h-[60vh] overflow-y-auto pr-1">
                    <div
                        class="rounded-xl border border-blue-500/20 bg-blue-500/10 p-3 text-xs text-blue-300 flex items-center gap-2">
                        <x-icons.info class="h-4 w-4 shrink-0 text-blue-400" />
                        <span>Mỗi đơn hàng tối đa 10 vé. Bạn có thể tăng hoặc giảm số lượng từng loại vé trong
                            phạm vi vé còn trống.</span>
                    </div>

                    <div class="space-y-3">
                        @foreach($order->event->ticketTypes->where('is_active', true) as $ticketType)
                            @php
                                $item = $order->items->firstWhere('ticket_type_id', $ticketType->id);
                                $currentQty = $item ? $item->quantity : 0;
                                // Tối đa có thể chọn = số lượng đang giữ trong đơn này + tồn kho khả dụng
                                $maxCanBook = $currentQty + $ticketType->quantity;
                            @endphp
                            <div
                                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 rounded-xl border border-white/5 bg-[#2a2a2a] p-3.5">
                                <div class="min-w-0">
                                    <h4 class="text-sm font-semibold text-white truncate">{{ $ticketType->name }}
                                    </h4>
                                    <div class="flex items-center gap-2 text-xs text-gray-400 mt-0.5">
                                        <span
                                            class="font-semibold text-green-400">{{ number_format($ticketType->price, 0, ',', '.') }}
                                            ₫</span>
                                        <span>•</span>
                                        <span>Còn khả dụng: <strong class="text-gray-300">{{ $maxCanBook }}</strong>
                                            vé</span>
                                    </div>
                                </div>

                                <!-- Bộ tăng giảm số lượng -->
                                <div class="flex items-center gap-2 self-end sm:self-auto shrink-0">
                                    <button type="button"
                                        onclick="changeModalTicketQty({{ $order->id }}, {{ $ticketType->id }}, -1)"
                                        class="h-8 w-8 rounded-lg border border-white/10 bg-white/5 flex items-center justify-center text-gray-300 hover:bg-white/10 hover:text-white transition text-sm font-bold cursor-pointer">
                                        -
                                    </button>
                                    <input type="number" id="qty-input-{{ $order->id }}-{{ $ticketType->id }}"
                                        name="tickets[{{ $ticketType->id }}]" value="{{ $currentQty }}" min="0"
                                        max="{{ min(10, $maxCanBook) }}" data-price="{{ $ticketType->price }}"
                                        data-max="{{ min(10, $maxCanBook) }}" data-order="{{ $order->id }}" readonly
                                        class="h-8 w-12 rounded-lg border border-white/10 bg-[#1e1e1e] text-center font-bold text-white text-sm focus:outline-none select-none" />
                                    <button type="button"
                                        onclick="changeModalTicketQty({{ $order->id }}, {{ $ticketType->id }}, 1)"
                                        class="h-8 w-8 rounded-lg border border-white/10 bg-white/5 flex items-center justify-center text-gray-300 hover:bg-white/10 hover:text-white transition text-sm font-bold cursor-pointer">
                                        +
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Tóm tắt số lượng và tổng tiền -->
                    <div class="rounded-xl border border-white/10 bg-[#1d1d1d] p-3.5 space-y-2 text-xs">
                        <div class="flex justify-between text-gray-400">
                            <span>Tổng số vé đã chọn:</span>
                            <span class="font-bold text-white"><span
                                    id="summary-qty-{{ $order->id }}">{{ $order->items->sum('quantity') }}</span>
                                / 10 vé</span>
                        </div>
                        <div class="flex justify-between text-gray-400">
                            <span>Tổng tiền mới:</span>
                            <span id="summary-price-{{ $order->id }}"
                                class="font-bold text-base text-green-400">{{ number_format($order->total_amount, 0, ',', '.') }}
                                ₫</span>
                        </div>
                        <p id="error-msg-{{ $order->id }}" class="hidden text-xs text-red-400 pt-1 font-medium">
                        </p>
                    </div>
                </div>

                <!-- Footer modal -->
                <div class="flex items-center justify-end gap-3 border-t border-white/10 pt-4">
                    <button type="button" onclick="closeEditTicketModal({{ $order->id }})"
                        class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-xs font-semibold text-gray-300 hover:bg-white/10 transition cursor-pointer">
                        Đóng
                    </button>
                    <button type="submit" id="submit-btn-{{ $order->id }}"
                        class="rounded-xl bg-blue-600 px-5 py-2 text-xs font-semibold text-white hover:bg-blue-500 transition cursor-pointer">
                        Lưu thay đổi
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
