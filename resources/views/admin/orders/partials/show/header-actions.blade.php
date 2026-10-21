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
            {{-- Nút xác nhận thanh toán (CHỈ cho đơn processing, KHÔNG cho đơn pending đang giữ chỗ) --}}
            @if ($order->status === 'processing')
                <form id="admin-confirm-order-form" action="{{ route('admin.orders.confirm', $order) }}" method="POST">
                    @csrf
                    <button type="submit" id="btn-admin-confirm"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700 transition shadow-xs cursor-pointer">
                        <x-icons.check />
                        <span>Xác nhận thanh toán</span>
                    </button>
                </form>
            @elseif ($order->status === 'pending')
                <span class="inline-flex items-center gap-1.5 rounded-lg border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-medium text-amber-700">
                    <x-icons.clock class="h-4 w-4" />
                    <span>Khách đang giữ chỗ (chưa thể xác nhận)</span>
                </span>
            @endif

            {{-- Nút hủy đơn hàng --}}
            <button type="button" onclick="openCancelModal()"
                class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-sm font-semibold text-red-600 hover:bg-red-100 transition shadow-2xs cursor-pointer">
                <x-icons.delete />
                <span>Hủy đơn hàng</span>
            </button>
        </div>
    @endif
</div>
