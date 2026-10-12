{{-- Bộ lọc trạng thái đơn hàng --}}
<div class="mb-6 flex overflow-x-auto pb-1 scrollbar-none gap-6 sm:gap-8">
    <a href="{{ route('user.tickets') }}"
        class="{{ !$status ? 'border-blue-600 text-blue-600 font-semibold' : 'border-transparent text-gray-400 hover:border-white hover:text-white' }} whitespace-nowrap border-b-2 px-1 py-2 text-sm font-medium transition">
        Tất cả
    </a>
    <a href="{{ route('user.tickets', ['status' => 'completed']) }}"
        class="{{ $status === 'completed' ? 'border-green-500 text-green-500 font-semibold' : 'border-transparent text-gray-400 hover:border-white hover:text-white' }} whitespace-nowrap border-b-2 px-1 py-2 text-sm font-medium transition">
        Thành công
    </a>
    <a href="{{ route('user.tickets', ['status' => 'pending']) }}"
        class="{{ $status === 'pending' ? 'border-amber-500 text-amber-500 font-semibold' : 'border-transparent text-gray-400 hover:border-white hover:text-white' }} whitespace-nowrap border-b-2 px-1 py-2 text-sm font-medium transition">
        Chờ xử lý
    </a>
    <a href="{{ route('user.tickets', ['status' => 'cancelled']) }}"
        class="{{ $status === 'cancelled' ? 'border-red-500 text-red-500 font-semibold' : 'border-transparent text-gray-400 hover:border-white hover:text-white' }} whitespace-nowrap border-b-2 px-1 py-2 text-sm font-medium transition">
        Đã hủy
    </a>
</div>
