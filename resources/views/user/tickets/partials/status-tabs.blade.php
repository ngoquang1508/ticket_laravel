{{-- Bộ lọc trạng thái đơn hàng (Hỗ trợ cuộn ngang mượt mà và responsive trên mọi thiết bị) --}}
<div class="relative mb-6">
    <div id="status-filters-container"
        class="flex items-center gap-4 sm:gap-6 md:gap-8 overflow-x-auto scroll-smooth border-b border-white/10 pb-2 [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden touch-pan-x">
        
        {{-- Tất cả --}}
        <a href="{{ route('user.tickets') }}"
            class="{{ !$status ? 'border-blue-500 text-blue-400 font-bold' : 'border-transparent text-gray-400 hover:border-gray-500 hover:text-gray-200' }} inline-flex items-center gap-1.5 whitespace-nowrap border-b-2 px-1 py-2 text-sm font-medium transition-colors shrink-0">
            <span>Tất cả</span>
        </a>

        {{-- Thành công --}}
        <a href="{{ route('user.tickets', ['status' => 'completed']) }}"
            class="{{ $status === 'completed' ? 'border-emerald-500 text-emerald-400 font-bold' : 'border-transparent text-gray-400 hover:border-gray-500 hover:text-gray-200' }} inline-flex items-center gap-2 whitespace-nowrap border-b-2 px-1 py-2 text-sm font-medium transition-colors shrink-0">
            <span class="h-2 w-2 rounded-full {{ $status === 'completed' ? 'bg-emerald-400 shadow-sm shadow-emerald-400/50' : 'bg-gray-500' }}"></span>
            <span>Thành công</span>
        </a>

        {{-- Chờ xử lý --}}
        <a href="{{ route('user.tickets', ['status' => 'pending']) }}"
            class="{{ $status === 'pending' ? 'border-amber-500 text-amber-400 font-bold' : 'border-transparent text-gray-400 hover:border-gray-500 hover:text-gray-200' }} inline-flex items-center gap-2 whitespace-nowrap border-b-2 px-1 py-2 text-sm font-medium transition-colors shrink-0">
            <span class="h-2 w-2 rounded-full {{ $status === 'pending' ? 'bg-amber-400 shadow-sm shadow-amber-400/50' : 'bg-gray-500' }}"></span>
            <span>Chờ xử lý</span>
        </a>

        {{-- Hết hạn --}}
        <a href="{{ route('user.tickets', ['status' => 'expired']) }}"
            class="{{ $status === 'expired' ? 'border-rose-500 text-rose-400 font-bold' : 'border-transparent text-gray-400 hover:border-gray-500 hover:text-gray-200' }} inline-flex items-center gap-2 whitespace-nowrap border-b-2 px-1 py-2 text-sm font-medium transition-colors shrink-0">
            <span class="h-2 w-2 rounded-full {{ $status === 'expired' ? 'bg-rose-400 shadow-sm shadow-rose-400/50' : 'bg-gray-500' }}"></span>
            <span>Hết hạn</span>
        </a>

        {{-- Đã hủy --}}
        <a href="{{ route('user.tickets', ['status' => 'cancelled']) }}"
            class="{{ $status === 'cancelled' ? 'border-red-500 text-red-400 font-bold' : 'border-transparent text-gray-400 hover:border-gray-500 hover:text-gray-200' }} inline-flex items-center gap-2 whitespace-nowrap border-b-2 px-1 py-2 text-sm font-medium transition-colors shrink-0">
            <span class="h-2 w-2 rounded-full {{ $status === 'cancelled' ? 'bg-red-400 shadow-sm shadow-red-400/50' : 'bg-gray-500' }}"></span>
            <span>Đã hủy</span>
        </a>
    </div>
</div>

{{-- Tự động cuộn mượt tab đang chọn vào vùng nhìn thấy trên thiết bị di động --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const container = document.getElementById('status-filters-container');
        if (container) {
            const activeTab = container.querySelector('.border-blue-500, .border-emerald-500, .border-amber-500, .border-rose-500, .border-red-500');
            if (activeTab) {
                activeTab.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
            }
        }
    });
</script>
