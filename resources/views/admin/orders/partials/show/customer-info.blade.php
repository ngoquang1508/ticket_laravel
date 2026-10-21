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
