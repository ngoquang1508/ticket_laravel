{{-- Bộ lọc & Tìm kiếm đơn hàng --}}
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
                <x-icons.search class="pointer-events-none absolute left-3 top-2.5 h-4 w-4 text-gray-400" />
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
                <option value="processing" @selected(request('status') === 'processing')>Đang xử lý (Chờ duyệt)</option>
                <option value="pending" @selected(request('status') === 'pending')>Đang giữ chỗ</option>
                <option value="cancelled" @selected(request('status') === 'cancelled')>Đã hủy (Khách/Admin hủy)</option>
                <option value="expired" @selected(request('status') === 'expired')>Hết hạn thanh toán</option>
            </select>
        </div>

        {{-- Phương thức thanh toán --}}
        <div>
            <label class="mb-1 block text-xs font-medium text-gray-600">Phương thức TT</label>
            <select name="payment_method"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                <option value="">Tất cả phương thức</option>
                <option value="vnpay" @selected(request('payment_method') === 'vnpay')>VNPay</option>
                <option value="direct" @selected(request('payment_method') === 'direct')>Trực tiếp / Chuyển khoản</option>
                <option value="free" @selected(request('payment_method') === 'free')>Miễn phí (0đ)</option>
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
