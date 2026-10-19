{{-- Bảng tóm tắt giỏ vé và nút tiếp tục thanh toán (Desktop) --}}
<div class="bg-[#1e1e1e] rounded-2xl border border-gray-800 shadow-xl lg:sticky lg:top-24 flex flex-col max-h-[calc(100vh-120px)]">
    <div class="p-6 border-b border-gray-800">
        <h2 class="text-xl font-bold text-white">Vé của bạn</h2>
    </div>

    <!-- Danh sách các vé/ghế đã chọn -->
    <div class="p-6 overflow-y-auto flex-1 custom-scrollbar" id="selected-tickets-list">
        <div class="text-gray-500 text-center italic py-8" id="empty-cart-msg">
            Chưa có vé nào được chọn.
        </div>
        <!-- Các vé/ghế đã chọn được thêm động qua JavaScript -->
    </div>

    <!-- Khung tính tiền và nút hành động -->
    <div class="p-6 border-t border-gray-800 bg-[#252525] rounded-b-2xl">
        <div class="flex justify-between items-center mb-4">
            <span class="text-gray-400">Tổng tạm tính</span>
            <span class="text-2xl font-bold text-green-500" id="total-price">0 đ</span>
        </div>
        <form id="checkout-form" action="{{ route('events.book.store', $event->slug) }}" method="POST">
            @csrf
            <input type="hidden" name="cart_data" id="cart-data-input">
            <button type="submit" id="btn-checkout"
                class="w-full py-4 rounded-xl font-bold text-lg transition duration-300 text-[#121212] bg-gray-500 cursor-not-allowed flex items-center justify-center gap-2"
                disabled>
                <span>Tiếp tục thanh toán</span>
            </button>
        </form>
    </div>
</div>
