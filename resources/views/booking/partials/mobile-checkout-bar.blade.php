{{-- Thanh thanh toán cố định ở đáy màn hình cho thiết bị di động (Mobile Responsive) --}}
<div class="lg:hidden fixed bottom-0 left-0 w-full bg-[#1e1e1e] border-t border-gray-800 shadow-[0_-10px_20px_rgba(0,0,0,0.5)] z-50 p-4 pb-safe flex items-center justify-between gap-4">
    <div class="flex flex-col">
        <span class="text-xs text-gray-400" id="mobile-ticket-count">0 vé</span>
        <span class="text-lg font-bold text-green-500" id="mobile-total-price">0 đ</span>
    </div>
    <button type="button" id="mobile-btn-checkout"
        class="px-6 py-3 rounded-xl font-bold transition duration-300 text-[#121212] bg-gray-500 cursor-not-allowed flex items-center justify-center gap-2"
        disabled>
        <span>Tiếp tục</span>
    </button>
</div>
