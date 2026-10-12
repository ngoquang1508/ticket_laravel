@extends('layouts.admin')

@section('title', 'Chi tiết đơn hàng #' . $order->order_code)
@section('page_description', 'Xem chi tiết đơn hàng, thông tin khách hàng và danh sách vé')

@section('content')
    <div class="mx-auto max-w-7xl">
        {{-- Thanh điều hướng & Thao tác đầu trang --}}
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.orders.index') }}"
                    class="rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition shadow-2xs">
                    ← Quay lại
                </a>
                <div>
                    <div class="flex items-center gap-2.5">
                        <h1 class="text-2xl font-bold tracking-tight text-gray-900 font-mono">#{{ $order->order_code }}</h1>
                        <x-order-status-badge :status="$order->status" />
                    </div>
                    <p class="mt-0.5 text-xs text-gray-500">
                        Đặt lúc {{ $order->created_at->format('d/m/Y H:i:s') }}
                    </p>
                </div>
            </div>

            {{-- Các nút thao tác xác nhận / hủy --}}
            @if (in_array($order->status, ['pending', 'processing']))
                <div class="flex flex-wrap items-center gap-2">
                    {{-- Nút xác nhận thanh toán --}}
                    <form action="{{ route('admin.orders.confirm', $order) }}" method="POST"
                        onsubmit="return confirm('Xác nhận đã nhận đủ tiền thanh toán cho đơn hàng #{{ $order->order_code }}?');">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700 transition shadow-xs cursor-pointer">
                            <x-icons.check />
                            <span>Xác nhận thanh toán</span>
                        </button>
                    </form>

                    {{-- Nút hủy đơn hàng --}}
                    <button type="button" onclick="openCancelModal()"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-sm font-semibold text-red-600 hover:bg-red-100 transition shadow-2xs cursor-pointer">
                        <x-icons.delete />
                        <span>Hủy đơn hàng</span>
                    </button>
                </div>
            @endif
        </div>

        {{-- Cảnh báo nếu đơn đã bị hủy --}}
        @if (in_array($order->status, ['cancelled', 'expired']))
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50/70 p-4 text-sm text-red-800">
                <div class="flex items-start gap-3">
                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600">!</span>
                    <div>
                        <h4 class="font-bold">Đơn hàng này đã bị {{ $order->status === 'expired' ? 'hết hạn giữ chỗ' : 'hủy bỏ' }}</h4>
                        <p class="mt-0.5 text-xs text-red-700">
                            Lý do: <span class="font-medium">{{ $order->cancel_reason ?: 'Không có ghi chú lý do' }}</span>
                        </p>
                        <p class="mt-1 text-xs text-red-600">Tất cả số lượng vé và vị trí ghế liên quan đã được giải phóng hoàn trả về sự kiện.</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Bố cục 2 cột chính --}}
        <div class="grid gap-6 lg:grid-cols-3">
            {{-- Cột trái (Chi tiết vé & sản phẩm) --}}
            <div class="space-y-6 lg:col-span-2">
                {{-- Bảng loại vé đã đặt --}}
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-2xs">
                    <div class="border-b border-gray-200 bg-gray-50/80 px-6 py-4">
                        <h2 class="font-bold text-gray-900">Chi tiết sản phẩm đặt mua</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="border-b border-gray-100 bg-white text-xs font-semibold uppercase text-gray-500">
                                <tr>
                                    <th class="px-6 py-3.5">Loại vé</th>
                                    <th class="px-6 py-3.5 text-right">Đơn giá</th>
                                    <th class="px-6 py-3.5 text-center">Số lượng</th>
                                    <th class="px-6 py-3.5 text-right">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($order->items as $item)
                                    <tr>
                                        <td class="px-6 py-4 font-medium text-gray-900">
                                            {{ $item->ticketType?->name ?: 'Loại vé không xác định' }}
                                        </td>
                                        <td class="px-6 py-4 text-right text-gray-600 font-mono">
                                            {{ number_format($item->price, 0, ',', '.') }}đ
                                        </td>
                                        <td class="px-6 py-4 text-center font-semibold text-gray-900">
                                            {{ $item->quantity }}
                                        </td>
                                        <td class="px-6 py-4 text-right font-bold text-gray-900 font-mono">
                                            {{ number_format($item->total, 0, ',', '.') }}đ
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="border-t border-gray-200 bg-gray-50/50">
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-right font-semibold text-gray-700">Tổng cộng:</td>
                                    <td class="px-6 py-4 text-right text-base font-extrabold text-primary-600 font-mono">
                                        {{ number_format($order->total_amount, 0, ',', '.') }}đ
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

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
            </div>

            {{-- Cột phải (Thông tin phụ trợ) --}}
            <div class="space-y-6">
                {{-- Thông tin thanh toán --}}
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-2xs">
                    <h3 class="font-bold text-gray-900 border-b pb-3 mb-4">Thông tin thanh toán</h3>
                    
                    <div class="mb-4 rounded-xl bg-gray-50 p-4 border border-gray-100">
                        <span class="text-xs uppercase tracking-wider text-gray-500 font-semibold block">Tổng thanh toán</span>
                        <span class="text-2xl font-black text-primary-600 font-mono mt-1 block">
                            {{ number_format($order->total_amount, 0, ',', '.') }}đ
                        </span>
                    </div>

                    <dl class="space-y-3 text-xs">
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Phương thức:</dt>
                            <dd class="font-semibold text-gray-900">
                                {{ $order->payment_method === 'vnpay' ? 'VNPay QR / Thẻ nội địa' : 'Trực tiếp / Chuyển khoản' }}
                            </dd>
                        </div>

                        <div class="flex justify-between">
                            <dt class="text-gray-500">Trạng thái thanh toán:</dt>
                            <dd class="font-semibold {{ $order->payment_status === 'paid' ? 'text-emerald-700' : 'text-amber-700' }}">
                                {{ $order->payment_status === 'paid' ? 'Đã thanh toán đủ' : ($order->payment_status === 'failed' ? 'Thất bại' : 'Chưa thanh toán') }}
                            </dd>
                        </div>

                        @if ($order->vnp_txn_ref)
                            <div class="flex justify-between border-t pt-2">
                                <dt class="text-gray-500">Mã GD VNPay:</dt>
                                <dd class="font-mono text-gray-700">{{ $order->vnp_txn_ref }}</dd>
                            </div>
                        @endif

                        @if ($order->expires_at && $order->status === 'pending')
                            <div class="flex justify-between border-t pt-2">
                                <dt class="text-gray-500">Hạn giữ chỗ:</dt>
                                <dd class="font-medium text-amber-700">{{ $order->expires_at->format('d/m/Y H:i:s') }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                {{-- Thông tin khách hàng --}}
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-2xs">
                    <h3 class="font-bold text-gray-900 border-b pb-3 mb-4">Thông tin khách hàng</h3>
                    <dl class="space-y-3 text-xs">
                        <div>
                            <dt class="text-gray-500 font-medium">Họ và tên</dt>
                            <dd class="mt-0.5 text-sm font-semibold text-gray-900">{{ $order->customer_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 font-medium">Email liên hệ</dt>
                            <dd class="mt-0.5 font-medium text-gray-900">{{ $order->customer_email }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 font-medium">Số điện thoại</dt>
                            <dd class="mt-0.5 font-medium text-gray-900">{{ $order->customer_phone ?: 'Chưa cập nhật' }}</dd>
                        </div>
                        @if ($order->user)
                            <div class="border-t pt-3">
                                <dt class="text-gray-500 font-medium">Tài khoản thành viên</dt>
                                <dd class="mt-1 flex items-center justify-between">
                                    <span class="font-medium text-gray-900">{{ $order->user->name }}</span>
                                    <a href="{{ route('admin.users.edit', $order->user) }}"
                                        class="text-xs font-semibold text-primary-600 hover:underline">
                                        Xem tài khoản →
                                    </a>
                                </dd>
                            </div>
                        @endif
                    </dl>
                </div>

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
            </div>
        </div>
    </div>

    {{-- Modal hủy đơn hàng --}}
    @if (in_array($order->status, ['pending', 'processing']))
        <div id="cancel-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/60 p-4 backdrop-blur-xs">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl transition-all">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Xác nhận hủy đơn hàng</h3>
                        <p class="mt-1 text-xs text-gray-500">Mã đơn: <span class="font-mono font-bold text-gray-800">#{{ $order->order_code }}</span></p>
                    </div>
                    <button type="button" onclick="closeCancelModal()" class="rounded-lg p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('admin.orders.cancel', $order) }}" method="POST" class="mt-4">
                    @csrf
                    <div class="mb-4">
                        <label class="mb-1.5 block text-xs font-semibold text-gray-700">Lý do hủy đơn</label>
                        <textarea name="cancel_reason" rows="3" required
                            placeholder="Nhập lý do hủy đơn hàng..."
                            class="w-full rounded-xl border border-gray-300 p-3 text-sm focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500">Quản trị viên hủy đơn hàng</textarea>
                        <p class="mt-1 text-[11px] text-gray-500">Lưu ý: Hành động này sẽ giải phóng ghế và hoàn trả số lượng vé về sự kiện.</p>
                    </div>

                    <div class="flex items-center justify-end gap-2.5">
                        <button type="button" onclick="closeCancelModal()"
                            class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 transition">
                            Đóng
                        </button>
                        <button type="submit"
                            class="rounded-lg bg-red-600 px-4 py-2 text-xs font-semibold text-white hover:bg-red-700 transition">
                            Xác nhận hủy đơn
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @push('scripts')
            <script>
                function openCancelModal() {
                    const modal = document.getElementById('cancel-modal');
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }

                function closeCancelModal() {
                    const modal = document.getElementById('cancel-modal');
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
            </script>
        @endpush
    @endif
@endsection
