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
                <x-icons.close class="h-5 w-5" />
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
