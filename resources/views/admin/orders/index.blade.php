@extends('layouts.admin')

@section('title', 'Quản lý đơn hàng')
@section('page_description', 'Danh sách và trạng thái các đơn hàng trong hệ thống')

@section('content')
    <div class="mx-auto max-w-7xl">
        {{-- Tiêu đề trang --}}
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Quản lý đơn hàng</h1>
                <p class="mt-1 text-sm text-gray-500">Theo dõi, kiểm tra và xử lý các đơn đặt vé của khách hàng.</p>
            </div>
        </div>

        {{-- Các thẻ thống kê nhanh --}}
        <div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-2 lg:grid-cols-5">
            {{-- Tổng đơn hàng --}}
            <div class="rounded-xl border border-gray-200/80 bg-white p-4.5 shadow-2xs transition hover:shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium uppercase tracking-wider text-gray-500">Tổng đơn hàng</span>
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100 text-gray-600">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </span>
                </div>
                <p class="mt-3 text-2xl font-bold text-gray-900">{{ number_format($metrics['total']) }}</p>
                <span class="mt-1 block text-xs text-gray-400">Tất cả thời gian</span>
            </div>

            {{-- Doanh thu hoàn tất --}}
            <div
                class="rounded-xl border border-emerald-200/60 bg-emerald-50/30 p-4.5 shadow-2xs transition hover:shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium uppercase tracking-wider text-emerald-700">Doanh thu đã thu</span>
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <p class="mt-3 text-2xl font-bold text-emerald-700">{{ number_format($metrics['revenue'], 0, ',', '.') }}đ
                </p>
                <span class="mt-1 block text-xs text-emerald-600">Đơn thành công</span>
            </div>

            {{-- Hoàn tất --}}
            <div class="rounded-xl border border-gray-200/80 bg-white p-4.5 shadow-2xs transition hover:shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium uppercase tracking-wider text-gray-500">Đã thanh toán</span>
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-green-100 text-green-600">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </span>
                </div>
                <p class="mt-3 text-2xl font-bold text-gray-900">{{ number_format($metrics['completed']) }}</p>
                <span class="mt-1 block text-xs text-gray-400">Giao dịch thành công</span>
            </div>

            {{-- Chờ xử lý --}}
            <div class="rounded-xl border border-gray-200/80 bg-white p-4.5 shadow-2xs transition hover:shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium uppercase tracking-wider text-gray-500">Chờ thanh toán</span>
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-100 text-amber-600">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <p class="mt-3 text-2xl font-bold text-gray-900">{{ number_format($metrics['pending']) }}</p>
                <span class="mt-1 block text-xs text-gray-400">Đang giữ chỗ</span>
            </div>

            {{-- Đã hủy --}}
            <div
                class="rounded-xl border border-gray-200/80 bg-white p-4.5 shadow-2xs transition hover:shadow-xs col-span-2 sm:col-span-1">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium uppercase tracking-wider text-gray-500">Đã hủy / Quá hạn</span>
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-100 text-red-600">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </span>
                </div>
                <p class="mt-3 text-2xl font-bold text-gray-900">{{ number_format($metrics['cancelled']) }}</p>
                <span class="mt-1 block text-xs text-gray-400">Vé đã được hoàn trả</span>
            </div>
        </div>

        {{-- Bộ lọc & Tìm kiếm --}}
        <div class="mb-6 rounded-xl border border-gray-200 bg-white p-4.5 shadow-2xs">
            <form method="GET" action="{{ route('admin.orders.index') }}"
                class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6">
                {{-- Ô tìm kiếm từ khóa --}}
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-xs font-medium text-gray-600">Tìm kiếm</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Mã đơn, tên khách, email, SĐT..."
                            class="w-full rounded-lg border border-gray-300 py-2 pl-9 pr-3 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                        <svg class="pointer-events-none absolute left-3 top-2.5 h-4 w-4 text-gray-400" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                {{-- Lọc sự kiện --}}
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-xs font-medium text-gray-600">Sự kiện</label>
                    <select name="event_id"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                        <option value="">Tất cả sự kiện</option>
                        @foreach ($events as $event)
                            <option value="{{ $event->id }}" @selected(request('event_id') == $event->id)>
                                {{ $event->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Lọc trạng thái đơn --}}
                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-600">Trạng thái đơn</label>
                    <select name="status"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                        <option value="">Tất cả</option>
                        <option value="completed" @selected(request('status') === 'completed')>Hoàn tất</option>
                        <option value="processing" @selected(request('status') === 'processing')>Đang xử lý</option>
                        <option value="pending" @selected(request('status') === 'pending')>Chờ thanh toán</option>
                        <option value="cancelled" @selected(request('status') === 'cancelled')>Đã hủy</option>
                        <option value="expired" @selected(request('status') === 'expired')>Hết hạn</option>
                    </select>
                </div>

                {{-- Phương thức thanh toán --}}
                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-600">Phương thức TT</label>
                    <select name="payment_method"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                        <option value="">Tất cả phương thức</option>
                        <option value="vnpay" @selected(request('payment_method') === 'vnpay')>VNPay</option>
                        <option value="direct" @selected(request('payment_method') === 'direct')>Trực tiếp / Chuyển khoản
                        </option>
                    </select>
                </div>

                {{-- Khoảng ngày tạo --}}
                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-600">Từ ngày</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                </div>

                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-600">Đến ngày</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                </div>

                {{-- Các nút hành động lọc --}}
                <div class="flex items-end gap-2 sm:col-span-2 lg:col-span-4 xl:col-span-6 justify-end pt-1">
                    <a href="{{ route('admin.orders.index') }}"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                        Đặt lại
                    </a>
                    <button type="submit"
                        class="rounded-lg bg-gray-900 px-5 py-2 text-sm font-semibold text-white hover:bg-gray-800 transition">
                        Lọc danh sách
                    </button>
                </div>
            </form>
        </div>

        {{-- Bảng danh sách đơn hàng --}}
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-2xs">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[950px] text-left text-sm">
                    <thead
                        class="border-b border-gray-200 bg-gray-50/80 text-xs font-semibold tracking-wider text-gray-600">
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
                                            <span
                                                class="font-medium text-gray-700">{{ $order->tickets_count ?: $order->items->sum('quantity') }}</span>
                                            vé
                                        </div>
                                    </div>
                                </td>

                                {{-- Thanh toán --}}
                                <td class="px-5 py-4">
                                    <div class="flex flex-col gap-1">
                                        <span
                                            class="inline-flex w-fit items-center rounded-md px-2 py-0.5 text-xs font-medium {{ $order->payment_method === 'vnpay' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-gray-100 text-gray-700' }}">
                                            {{ $order->payment_method === 'vnpay' ? 'VNPay' : 'Trực tiếp' }}
                                        </span>
                                        <span
                                            class="text-[11px] {{ $order->payment_status === 'paid' ? 'text-emerald-600 font-semibold' : 'text-gray-500' }}">
                                            {{ $order->payment_status === 'paid' ? '✓ Đã thanh toán' : ($order->payment_status === 'failed' ? 'Thất bại' : 'Chưa thanh toán') }}
                                        </span>
                                    </div>
                                </td>

                                {{-- Trạng thái đơn --}}
                                <td class="px-5 py-4">
                                    <x-order-status-badge :status="$order->status" />
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

                                        {{-- Nút xác nhận thanh toán (cho pending / processing) --}}
                                        @if (in_array($order->status, ['pending', 'processing']))
                                            <form action="{{ route('admin.orders.confirm', $order) }}" method="POST"
                                                onsubmit="return confirm('Bạn có chắc chắn muốn xác nhận thanh toán cho đơn hàng #{{ $order->order_code }}?');"
                                                class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="rounded-lg border border-emerald-200 bg-emerald-50/50 px-2.5 py-1.5 text-xs font-medium text-emerald-600 transition hover:bg-emerald-100 hover:border-emerald-300 cursor-pointer shadow-2xs"
                                                    title="Xác nhận thanh toán">
                                                    <x-icons.check />
                                                </button>
                                            </form>

                                            {{-- Nút hủy đơn hàng --}}
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

        {{-- Phân trang --}}
        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    </div>

    {{-- Modal xác nhận hủy đơn hàng kèm nhập lý do --}}
    <div id="cancel-modal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/60 p-4 backdrop-blur-xs">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl transition-all">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Xác nhận hủy đơn hàng</h3>
                    <p class="mt-1 text-xs text-gray-500">Đơn hàng <span id="modal-order-code"
                            class="font-mono font-bold text-gray-700"></span></p>
                </div>
                <button type="button" onclick="closeCancelModal()"
                    class="rounded-lg p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="cancel-form" method="POST" class="mt-4">
                @csrf
                <div class="mb-4">
                    <label class="mb-1.5 block text-xs font-semibold text-gray-700">Lý do hủy đơn</label>
                    <textarea name="cancel_reason" rows="3" required
                        placeholder="Nhập lý do hủy (ví dụ: Khách yêu cầu hủy, Quá hạn thanh toán...)"
                        class="w-full rounded-xl border border-gray-300 p-3 text-sm focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500">Quản trị viên hủy đơn hàng</textarea>
                    <p class="mt-1 text-[11px] text-gray-500">Lưu ý: Ghế và số lượng vé trong đơn sẽ được hoàn trả tự động
                        vào kho vé.</p>
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
            function openCancelModal(orderCode, actionUrl) {
                document.getElementById('modal-order-code').textContent = '#' + orderCode;
                document.getElementById('cancel-form').action = actionUrl;
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
@endsection