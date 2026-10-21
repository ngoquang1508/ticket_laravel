{{-- Bảng danh sách đơn hàng --}}
<div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-2xs">
    <div class="overflow-x-auto">
        <table class="w-full min-w-[950px] text-left text-sm">
            <thead class="border-b border-gray-200 bg-gray-50/80 text-xs font-semibold tracking-wider text-gray-600">
                <tr>
                    <th class="px-5 py-4">Mã đơn & Thời gian</th>
                    <th class="px-5 py-4">Khách hàng</th>
                    <th class="px-5 py-4">Sự kiện</th>
                    <th class="px-5 py-4">Số vé & Tổng tiền</th>
                    <th class="px-5 py-4">Thanh toán</th>
                    <th class="px-5 py-4">Trạng thái</th>
                    <th class="px-5 py-4 text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($orders as $order)
                    <tr class="hover:bg-gray-50/80 transition-colors">
                        {{-- Mã đơn & Ngày giờ --}}
                        <td class="px-5 py-4">
                            <div class="flex flex-col">
                                <a href="{{ route('admin.orders.show', $order) }}"
                                    class="font-mono text-sm font-bold text-primary-600 hover:text-primary-700 hover:underline">
                                    #{{ $order->order_code }}
                                </a>
                                <span class="mt-0.5 text-xs text-gray-500">
                                    {{ $order->created_at->format('d/m/Y H:i') }}
                                </span>
                            </div>
                        </td>

                        {{-- Khách hàng --}}
                        <td class="px-5 py-4">
                            <div class="max-w-[190px]">
                                <div class="font-semibold text-gray-900 line-clamp-1">
                                    {{ $order->customer_name }}
                                </div>
                                <div class="mt-0.5 text-xs text-gray-500 truncate" title="{{ $order->customer_email }}">
                                    {{ $order->customer_email }}
                                </div>
                                @if ($order->customer_phone)
                                    <div class="text-xs text-gray-500">
                                        {{ $order->customer_phone }}
                                    </div>
                                @endif
                            </div>
                        </td>

                        {{-- Sự kiện --}}
                        <td class="px-5 py-4">
                            <div class="max-w-[220px]">
                                <a href="{{ route('admin.events.show', $order->event) }}"
                                    class="font-medium text-gray-900 hover:text-primary-600 line-clamp-1 transition"
                                    title="{{ $order->event->name }}">
                                    {{ $order->event->name }}
                                </a>
                                <span class="mt-0.5 block text-xs text-gray-500 truncate">
                                    {{ $order->event->location?->name ?: 'N/A' }}
                                </span>
                            </div>
                        </td>

                        {{-- Số vé & Tổng tiền --}}
                        <td class="px-5 py-4">
                            <div>
                                <span class="text-sm font-bold text-gray-900">
                                    {{ number_format($order->total_amount, 0, ',', '.') }}đ
                                </span>
                                <div class="mt-0.5 flex items-center gap-1.5 text-xs text-gray-500">
                                    <span class="font-medium text-gray-700">
                                        {{ $order->tickets_count ?: $order->items->sum('quantity') }}
                                    </span>
                                    vé
                                </div>
                            </div>
                        </td>

                        {{-- Thanh toán --}}
                        <td class="px-5 py-4">
                            <div class="flex flex-col gap-1">
                                @if ($order->payment_method === 'vnpay')
                                    <span
                                        class="inline-flex w-fit items-center rounded-md px-2 py-0.5 text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        VNPay
                                    </span>
                                @elseif ($order->payment_method === 'direct')
                                    <span
                                        class="inline-flex w-fit items-center rounded-md px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-700">
                                        Trực tiếp
                                    </span>
                                @elseif ($order->payment_method === 'free')
                                    <span
                                        class="inline-flex w-fit items-center rounded-md px-2 py-0.5 text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Miễn phí (0đ)
                                    </span>
                                @else
                                    <span
                                        class="inline-flex w-fit items-center rounded-md px-2 py-0.5 text-xs font-medium bg-gray-50 text-gray-500 border border-gray-200">
                                        Chưa chọn
                                    </span>
                                @endif
                                <span
                                    class="text-[11px] {{ $order->payment_status === 'paid' ? 'text-emerald-600 font-semibold' : 'text-gray-500' }}">
                                    @if ($order->payment_method === 'free')
                                        ✓ Miễn phí
                                    @else
                                        {{ $order->payment_status === 'paid' ? '✓ Đã thanh toán' : ($order->payment_status === 'failed' ? 'Thất bại' : 'Chưa thanh toán') }}
                                    @endif
                                </span>
                            </div>
                        </td>

                        {{-- Trạng thái đơn --}}
                        <td class="px-5 py-4">
                            <div class="flex flex-col gap-1">
                                <div>
                                    <x-order-status-badge :status="$order->status" />
                                </div>
                                @if ($order->status === 'pending' && $order->expires_at)
                                    @if ($order->expires_at > now())
                                        <span class="text-[11px] font-medium text-amber-600">
                                            Còn {{ (int) ceil(now()->diffInSeconds($order->expires_at) / 60) }}p giữ chỗ
                                        </span>
                                    @else
                                        <span class="text-[11px] font-medium text-red-500">
                                            Đã quá hạn
                                        </span>
                                    @endif
                                @elseif ($order->status === 'cancelled' && $order->cancel_reason)
                                    <span class="text-[11px] text-gray-400 max-w-[140px] truncate"
                                        title="{{ $order->cancel_reason }}">
                                        {{ $order->cancel_reason }}
                                    </span>
                                @endif
                            </div>
                        </td>

                        {{-- Cột thao tác --}}
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                {{-- Nút xem chi tiết --}}
                                <a href="{{ route('admin.orders.show', $order) }}"
                                    class="rounded-lg border border-gray-200 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 transition hover:border-gray-300 hover:bg-gray-50 hover:text-primary-600 shadow-2xs"
                                    title="Xem chi tiết đơn hàng">
                                    <x-icons.eye />
                                </a>

                                {{-- Nút xác nhận thanh toán (CHỈ cho đơn processing, KHÔNG cho đơn pending đang giữ chỗ) --}}
                                @if ($order->status === 'processing')
                                    <form action="{{ route('admin.orders.confirm', $order) }}" method="POST"
                                        class="inline quick-confirm-form" data-order-code="{{ $order->order_code }}">
                                        @csrf
                                        <button type="submit"
                                            class="rounded-lg border border-emerald-200 bg-emerald-50/50 px-2.5 py-1.5 text-xs font-medium text-emerald-600 transition hover:bg-emerald-100 hover:border-emerald-300 cursor-pointer shadow-2xs"
                                            title="Xác nhận thanh toán">
                                            <x-icons.check />
                                        </button>
                                    </form>
                                @endif

                                {{-- Nút hủy đơn hàng (cho cả pending và processing) --}}
                                @if (in_array($order->status, ['pending', 'processing']))
                                    <button type="button"
                                        onclick="openCancelModal('{{ $order->order_code }}', '{{ route('admin.orders.cancel', $order) }}')"
                                        class="rounded-lg border border-red-200 bg-red-50/50 px-2.5 py-1.5 text-xs font-medium text-red-600 transition hover:bg-red-100 hover:border-red-300 cursor-pointer shadow-2xs"
                                        title="Hủy đơn hàng">
                                        <x-icons.delete />
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <div
                                class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-400 mb-3">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                            </div>
                            <p class="font-medium text-gray-900">Không tìm thấy đơn hàng nào</p>
                            <p class="mt-1 text-xs text-gray-500">Thử thay đổi bộ lọc tìm kiếm hoặc từ khóa tra cứu.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Phân trang danh sách --}}
<div class="mt-6">
    {{ $orders->links() }}
</div>
