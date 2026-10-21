{{--
Modal Thanh toán (Checkout Modal Partial)
Chỉ chứa HTML/CSS/JS của modal thanh toán, không chứa trang nền.
Sử dụng trực tiếp tại: Trang đặt vé (booking.show) và Trang vé của tôi (user.tickets).
--}}

<div id="checkout-modal-{{ $order->id }}" onclick="if(event.target === this) closeCheckoutModal({{ $order->id }})"
    class="fixed inset-0 z-50 overflow-y-auto bg-black/80 backdrop-blur-sm p-3 sm:p-4 flex items-center justify-center hidden transition-all duration-300 checkout-modal-backdrop"
    data-order-id="{{ $order->id }}" data-order-code="{{ $order->order_code }}"
    data-expires-at="{{ $order->expires_at->toIso8601String() }}">

    <!-- Container Modal (Giới hạn chiều cao max-h, header cố định và body cuộn mượt mà) -->
    <div id="checkout-modal-container-{{ $order->id }}"
        class="relative w-full max-w-lg my-auto rounded-2xl bg-[#222222] border border-white/10 text-white shadow-2xl flex flex-col max-h-[92vh] sm:max-h-[90vh] overflow-hidden animate-fade-in-up">

        @php
            $isFreeOrder = ($order->total_amount <= 0);
        @endphp

        <!-- Header Modal (Cố định ở trên cùng, luôn nhìn thấy nút X và tiêu đề) -->
        <div
            class="shrink-0 flex items-center justify-between border-b border-white/10 px-4 py-3 sm:px-6 sm:py-3.5 bg-[#222222] z-10">
            <div class="min-w-0 pr-2">
                <h2 class="text-base sm:text-lg font-bold text-white truncate">
                    {{ $isFreeOrder ? 'Xác nhận nhận vé miễn phí' : 'Hoàn tất thanh toán' }}
                </h2>
                <p class="text-gray-400 text-xs mt-0.5 line-clamp-1">
                    {{ $isFreeOrder ? 'Vé của bạn đang được giữ chỗ. Vui lòng xác nhận thông tin nhận vé.' : 'Ghế của bạn đang được giữ chỗ trong thời gian đếm ngược' }}
                </p>
            </div>

            <!-- Nút đóng 'X' modal thuần túy -->
            <button type="button" onclick="closeCheckoutModal({{ $order->id }})"
                class="shrink-0 text-gray-400 hover:text-white hover:bg-white/10 p-1.5 sm:p-2 rounded-xl transition duration-150 flex items-center justify-center cursor-pointer"
                title="Đóng modal">
                <x-icons.close class="w-5 h-5" />
            </button>
        </div>

        <!-- Thân Modal (Cuộn nội dung độc lập, thanh cuộn mượt mà) -->
        <div class="overflow-y-auto p-4 sm:p-5 space-y-3.5 sm:space-y-4 custom-scrollbar">
            <!-- Đồng hồ đếm ngược giữ vé -->
            <div class="flex justify-center">
                <div id="countdown-wrapper-{{ $order->id }}"
                    class="w-full sm:w-auto {{ $isFreeOrder ? 'bg-emerald-500/10 border-emerald-500/20' : 'bg-amber-500/10 border-amber-500/20' }} border rounded-xl px-4 py-2 sm:px-6 sm:py-2.5 text-center transition-all duration-300">
                    <span class="block text-[11px] font-semibold {{ $isFreeOrder ? 'text-emerald-400' : 'text-amber-400' }} uppercase tracking-wider mb-0.5">
                        {{ $isFreeOrder ? 'Thời gian giữ vé miễn phí còn lại' : 'Thời gian giữ vé còn lại' }}
                    </span>
                    <div class="text-2xl sm:text-3xl font-mono font-bold {{ $isFreeOrder ? 'text-emerald-400' : 'text-amber-400' }} tracking-wider countdown-display"
                        id="countdown-{{ $order->id }}">10:00</div>
                </div>
            </div>

            <!-- Thông tin đơn hàng tóm tắt -->
            <div class="bg-[#292929] rounded-xl p-3 sm:p-4 border border-white/10 text-xs sm:text-sm">
                <div class="flex justify-between items-center mb-1.5">
                    <span class="text-gray-400">Mã đơn hàng:</span>
                    <span class="font-mono font-bold text-white tracking-wide">{{ $order->order_code }}</span>
                </div>
                <div class="flex justify-between items-start mb-1.5 gap-2">
                    <span class="text-gray-400 shrink-0">Sự kiện:</span>
                    <span class="font-medium text-white text-right line-clamp-1">{{ $order->event->name }}</span>
                </div>
                <div class="flex justify-between items-center pt-2 border-t border-white/10 mt-1.5">
                    <span class="text-gray-400 font-medium">Tổng tiền:</span>
                    <span
                        class="text-base sm:text-lg font-semibold text-emerald-400">{{ number_format($order->total_amount, 0, ',', '.') }}
                        đ</span>
                </div>
            </div>

            <!-- Các hành động thanh toán (khi đơn còn hiệu lực) -->
            <div id="payment-actions-{{ $order->id }}">
                <form id="payment-form-{{ $order->id }}"
                    action="{{ route('orders.process-payment', $order->order_code) }}" method="POST">
                    @csrf

                    @php
                        $user = auth()->user();
                        $customerName = old('customer_name', $order->customer_name ?: ($user?->name ?? ''));
                        $customerEmail = old('customer_email', $order->customer_email ?: ($user?->email ?? ''));
                        $customerPhone = old('customer_phone', $order->customer_phone ?: ($user?->phone ?? ''));
                        $hasPhone = !empty($customerPhone);
                    @endphp

                    <!-- Khối thông tin người nhận vé (Compact Preview Card) -->
                    <div class="bg-[#292929] rounded-xl p-3 sm:p-3.5 border border-white/10 text-xs mb-3.5 sm:mb-4 transition-all duration-200" id="customer-box-{{ $order->id }}">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="p-1 rounded-lg bg-emerald-500/10 text-emerald-400 shrink-0">
                                    <x-icons.user class="w-4 h-4" />
                                </span>
                                <span class="font-semibold text-white truncate text-xs sm:text-sm">Thông tin nhận vé</span>
                            </div>

                            <!-- Nút mở rộng/thu gọn form chỉnh sửa -->
                            <button type="button" id="btn-toggle-customer-{{ $order->id }}"
                                class="text-[11px] text-emerald-400 hover:text-emerald-300 font-medium px-2 py-1 rounded hover:bg-emerald-500/10 transition cursor-pointer flex items-center gap-1 shrink-0">
                                <span id="btn-toggle-text-{{ $order->id }}">{{ $hasPhone ? 'Thay đổi' : 'Bổ sung SĐT' }}</span>
                                <x-icons.chevron-down id="btn-toggle-icon-{{ $order->id }}" class="w-3.5 h-3.5 transition-transform duration-200 {{ $hasPhone ? '' : 'rotate-180' }}" />
                            </button>
                        </div>

                        <!-- Dòng tóm tắt nhanh (hiển thị mặc định nếu đã có số điện thoại) -->
                        <div id="customer-preview-{{ $order->id }}" class="{{ $hasPhone ? '' : 'hidden' }} mt-2 pt-2 border-t border-white/5 space-y-0.5">
                            <div class="flex items-center justify-between text-xs gap-2">
                                <span class="text-white font-medium truncate" id="preview-name-{{ $order->id }}">{{ $customerName ?: 'Chưa có tên' }}</span>
                                <span class="text-gray-300 font-mono text-[11px] shrink-0" id="preview-phone-{{ $order->id }}">{{ $customerPhone ?: 'Chưa có SĐT' }}</span>
                            </div>
                            <div class="text-[11px] text-gray-400 truncate" id="preview-email-{{ $order->id }}">{{ $customerEmail ?: 'Chưa có email' }}</div>
                        </div>

                        <!-- Form chi tiết (Mở rộng khi click nút Thay đổi hoặc khi chưa có SĐT) -->
                        <div id="customer-form-fields-{{ $order->id }}" class="{{ $hasPhone ? 'hidden' : '' }} space-y-2.5 mt-2.5 pt-2.5 border-t border-white/10">
                            <div>
                                <label class="block text-[11px] text-gray-400 font-medium mb-1">
                                    Họ và tên người nhận <span class="text-red-400">*</span>
                                </label>
                                <input type="text" name="customer_name" id="input-name-{{ $order->id }}" value="{{ $customerName }}" required
                                    class="w-full bg-[#1e1e1e] border border-white/10 rounded-lg px-3 py-1.5 sm:py-2 text-xs sm:text-sm text-white focus:outline-none focus:border-emerald-500 transition placeholder-gray-500"
                                    placeholder="VD: Nguyễn Văn A">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-[11px] text-gray-400 font-medium mb-1">
                                        Email nhận mã vé <span class="text-red-400">*</span>
                                    </label>
                                    <input type="email" name="customer_email" id="input-email-{{ $order->id }}" value="{{ $customerEmail }}" required
                                        class="w-full bg-[#1e1e1e] border border-white/10 rounded-lg px-3 py-1.5 sm:py-2 text-xs sm:text-sm text-white focus:outline-none focus:border-emerald-500 transition placeholder-gray-500"
                                        placeholder="email@example.com">
                                </div>
                                <div>
                                    <label class="block text-[11px] text-gray-400 font-medium mb-1 flex items-center justify-between">
                                        <span>Số điện thoại <span class="text-red-400">*</span></span>
                                        @if(!$hasPhone)
                                            <span class="text-[10px] text-amber-400 font-normal">Cần bổ sung</span>
                                        @endif
                                    </label>
                                    <input type="tel" name="customer_phone" id="input-phone-{{ $order->id }}" value="{{ $customerPhone }}" required
                                        pattern="^(0|\+84)[35789][0-9]{8}$"
                                        class="w-full bg-[#1e1e1e] border border-white/10 rounded-lg px-3 py-1.5 sm:py-2 text-xs sm:text-sm text-white focus:outline-none focus:border-emerald-500 transition placeholder-gray-500"
                                        placeholder="0912345678">
                                </div>
                            </div>
                            <p class="text-[10px] text-gray-400 italic flex items-center gap-1.5">
                                <x-icons.info class="w-3.5 h-3.5 text-emerald-400 shrink-0" />
                                <span>Vé điện tử và mã QR check-in sự kiện sẽ được gửi tới địa chỉ email này.</span>
                            </p>
                        </div>
                    </div>

                    @if($isFreeOrder)
                        {{-- Trường hợp đơn hàng miễn phí (0 đ) --}}
                        <input type="hidden" name="payment_method" value="free">
                        <div class="mb-3.5 sm:mb-4 p-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
                                <x-icons.check-circle class="w-5 h-5" />
                            </div>
                            <div class="text-xs">
                                <div class="font-bold text-white">Đơn hàng miễn phí (0 đ)</div>
                                <div class="text-gray-400">Bạn không cần thanh toán bất kỳ chi phí nào. Nhấn nút bên dưới để nhận vé ngay.</div>
                            </div>
                        </div>

                        <!-- Nút xác nhận nhận vé miễn phí -->
                        <button type="submit" id="btn-pay-{{ $order->id }}"
                            class="w-full bg-emerald-500 text-white font-bold py-2.5 sm:py-3 rounded-xl hover:bg-emerald-600 transition shadow-lg shadow-emerald-500/20 text-xs sm:text-sm cursor-pointer flex items-center justify-center gap-2">
                            <span>Xác nhận nhận vé (Miễn phí)</span>
                            <x-icons.arrow-right class="w-4 h-4" />
                        </button>
                    @else
                        {{-- Trường hợp đơn hàng có phí cần thanh toán --}}
                        <h3 class="font-semibold text-white mb-2 text-xs sm:text-sm flex items-center gap-2">
                            <span>Chọn hình thức thanh toán:</span>
                        </h3>

                        @php
                            $canPayDirect = $order->event ? $order->event->canPayDirect() : false;
                        @endphp

                        <div class="space-y-2 mb-3.5 sm:mb-4">
                            <!-- Lựa chọn VNPay -->
                            <label
                                class="flex items-center justify-between p-2.5 sm:p-3 border border-white/10 rounded-xl cursor-pointer bg-[#292929] hover:bg-white/5 transition has-[:checked]:border-emerald-500/60 has-[:checked]:bg-emerald-500/10 has-[:checked]:ring-1 has-[:checked]:ring-emerald-500/50">
                                <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                                    <input type="radio" name="payment_method" value="vnpay"
                                        class="w-4 h-4 text-emerald-500 border-white/20 bg-black/40 focus:ring-emerald-500 shrink-0"
                                        checked>
                                    <div class="min-w-0">
                                        <span class="block text-xs sm:text-sm font-semibold text-white truncate">Thanh toán
                                            qua VNPay</span>
                                        <span class="block text-[11px] text-gray-400 truncate">Quét mã QR, Thẻ ATM,
                                            Visa/Mastercard</span>
                                    </div>
                                </div>
                            </label>

                            <!-- Lựa chọn Thanh toán trực tiếp -->
                            @if($canPayDirect)
                                <label
                                    class="flex items-center justify-between p-2.5 sm:p-3 border border-white/10 rounded-xl cursor-pointer bg-[#292929] hover:bg-white/5 transition has-[:checked]:border-emerald-500/60 has-[:checked]:bg-emerald-500/10 has-[:checked]:ring-1 has-[:checked]:ring-emerald-500/50">
                                    <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                                        <input type="radio" name="payment_method" value="direct"
                                            class="w-4 h-4 text-emerald-500 border-white/20 bg-black/40 focus:ring-emerald-500 shrink-0">
                                        <div class="min-w-0">
                                            <span class="block text-xs sm:text-sm font-semibold text-white truncate">Thanh toán
                                                trực tiếp</span>
                                            <span class="block text-[11px] text-gray-400 truncate">Chuyển khoản thủ công hoặc
                                                tại quầy</span>
                                        </div>
                                    </div>
                                </label>
                            @else
                                <div
                                    class="flex items-center justify-between p-2.5 sm:p-3 border border-white/5 rounded-xl bg-[#202020] opacity-50 cursor-not-allowed">
                                    <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                                        <input type="radio" disabled
                                            class="w-4 h-4 text-gray-600 border-white/10 bg-black/20 shrink-0">
                                        <div class="min-w-0">
                                            <span class="block text-xs sm:text-sm font-semibold text-gray-400 truncate">Thanh
                                                toán trực tiếp</span>
                                            <span class="block text-[11px] text-red-400 truncate">Đã ngừng nhận (sự kiện diễn ra
                                                trong vòng 48h)</span>
                                        </div>
                                    </div>
                                    <span
                                        class="shrink-0 text-[10px] font-semibold px-2 py-0.5 rounded bg-red-500/10 text-red-400 border border-red-500/20">Đã
                                        đóng</span>
                                </div>
                            @endif
                        </div>

                        <!-- Nút xác nhận thanh toán -->
                        <button type="submit" id="btn-pay-{{ $order->id }}"
                            class="w-full bg-emerald-500 text-white font-bold py-2.5 sm:py-3 rounded-xl hover:bg-emerald-600 transition shadow-lg shadow-emerald-500/20 text-xs sm:text-sm cursor-pointer flex items-center justify-center gap-2">
                            <span>Tiến hành thanh toán</span>
                            <x-icons.arrow-right class="w-4 h-4" />
                        </button>
                    @endif
                </form>

                <!-- Nút thao tác phụ: Đóng modal và Hủy đơn hàng -->
                <div class="mt-2.5 sm:mt-3 grid grid-cols-2 gap-2">
                    <button type="button" onclick="closeCheckoutModal({{ $order->id }})"
                        class="text-center bg-white/5 text-gray-300 border border-white/10 font-semibold py-2 sm:py-2.5 px-2 rounded-xl hover:bg-white/10 hover:text-white transition text-xs flex items-center justify-center truncate cursor-pointer">
                        Đóng modal (Giữ vé)
                    </button>

                    <form id="cancel-form-{{ $order->id }}" action="{{ route('orders.cancel', $order->order_code) }}"
                        method="POST"
                        onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này không? Ghế đang giữ sẽ được giải phóng ngay lập tức.');">
                        @csrf
                        <button type="submit" id="btn-cancel-{{ $order->id }}"
                            class="w-full bg-white/5 text-gray-400 border border-white/10 font-semibold py-2 sm:py-2.5 px-2 rounded-xl hover:bg-red-500/10 hover:text-red-400 hover:border-red-500/20 transition text-xs cursor-pointer truncate">
                            Hủy đơn hàng
                        </button>
                    </form>
                </div>
            </div>

            <!-- Hành động khi thời gian giữ vé đã hết (Ẩn mặc định) -->
            <div id="expired-actions-{{ $order->id }}" class="hidden">
                <div class="text-center py-3 space-y-2.5">
                    <p class="text-xs sm:text-sm text-red-400">Đơn hàng đã hết hạn giữ vé và các ghế đã được mở lại cho
                        người khác.</p>
                    <a href="{{ route('events.book', $order->event->slug) }}"
                        class="block w-full text-center bg-emerald-500 text-white font-bold py-2.5 sm:py-3 rounded-xl hover:bg-emerald-600 transition text-xs sm:text-sm">
                        Đặt lại vé ngay
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Script đếm ngược riêng cho từng đơn hàng --}}
<script>
    (function () {
        const orderId = "{{ $order->id }}";
        const expiresAt = new Date("{{ $order->expires_at->toIso8601String() }}").getTime();
        const countdownEl = document.getElementById('countdown-' + orderId);
        const countdownWrapper = document.getElementById('countdown-wrapper-' + orderId);
        const paymentActions = document.getElementById('payment-actions-' + orderId);
        const expiredActions = document.getElementById('expired-actions-' + orderId);
        let timer = null;

        function updateCountdown() {
            const now = new Date().getTime();
            const distance = expiresAt - now;

            if (distance <= 0) {
                if (countdownEl) countdownEl.innerHTML = "00:00";
                if (countdownWrapper) {
                    countdownWrapper.classList.remove('bg-amber-500/10', 'border-amber-500/20');
                    countdownWrapper.classList.add('bg-red-500/10', 'border-red-500/20');
                }
                if (countdownEl) {
                    countdownEl.classList.remove('text-amber-400');
                    countdownEl.classList.add('text-red-400');
                }

                // Ẩn form thanh toán, hiển thị hành động khi hết hạn
                if (paymentActions) paymentActions.classList.add('hidden');
                if (expiredActions) expiredActions.classList.remove('hidden');

                clearInterval(timer);

                // Gửi request hủy đơn và giải phóng ghế trong nền
                fetch("{{ route('orders.cancel', $order->order_code) }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });

                return;
            }

            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            const timeStr = (minutes < 10 ? "0" + minutes : minutes) + ":" + (seconds < 10 ? "0" + seconds : seconds);
            if (countdownEl) countdownEl.innerHTML = timeStr;
        }

        timer = setInterval(updateCountdown, 1000);
        updateCountdown();

        // Xử lý thông tin người nhận vé (Compact Preview Card & Realtime Sync)
        const toggleBtn = document.getElementById('btn-toggle-customer-' + orderId);
        const toggleText = document.getElementById('btn-toggle-text-' + orderId);
        const toggleIcon = document.getElementById('btn-toggle-icon-' + orderId);
        const formFields = document.getElementById('customer-form-fields-' + orderId);
        const previewBox = document.getElementById('customer-preview-' + orderId);

        const inputName = document.getElementById('input-name-' + orderId);
        const inputEmail = document.getElementById('input-email-' + orderId);
        const inputPhone = document.getElementById('input-phone-' + orderId);

        const previewName = document.getElementById('preview-name-' + orderId);
        const previewEmail = document.getElementById('preview-email-' + orderId);
        const previewPhone = document.getElementById('preview-phone-' + orderId);

        if (toggleBtn && formFields) {
            toggleBtn.addEventListener('click', () => {
                const isHidden = formFields.classList.contains('hidden');
                if (isHidden) {
                    formFields.classList.remove('hidden');
                    if (previewBox) previewBox.classList.add('hidden');
                    if (toggleText) toggleText.innerText = 'Thu gọn';
                    if (toggleIcon) toggleIcon.classList.add('rotate-180');
                } else {
                    formFields.classList.add('hidden');
                    if (previewBox) previewBox.classList.remove('hidden');
                    if (toggleText) toggleText.innerText = 'Thay đổi';
                    if (toggleIcon) toggleIcon.classList.remove('rotate-180');
                }
            });
        }

        // Cập nhật realtime preview khi người dùng nhập dữ liệu
        if (inputName && previewName) {
            inputName.addEventListener('input', (e) => {
                previewName.innerText = e.target.value.trim() || 'Chưa có tên';
            });
        }
        if (inputEmail && previewEmail) {
            inputEmail.addEventListener('input', (e) => {
                previewEmail.innerText = e.target.value.trim() || 'Chưa có email';
            });
        }
        if (inputPhone && previewPhone) {
            inputPhone.addEventListener('input', (e) => {
                previewPhone.innerText = e.target.value.trim() || 'Chưa có SĐT';
            });
        }

        // Chống spam click nút Tiến hành thanh toán & tự động mở form nếu thiếu trường
        const paymentForm = document.getElementById('payment-form-' + orderId);
        const payBtn = document.getElementById('btn-pay-' + orderId);
        let isPaymentSubmitting = false;
        if (paymentForm && payBtn) {
            paymentForm.addEventListener('submit', (e) => {
                // Nếu dữ liệu chưa hợp lệ và form đang bị thu gọn, tự động bung ra để người dùng thấy lỗi
                if (!paymentForm.checkValidity()) {
                    if (formFields && formFields.classList.contains('hidden')) {
                        formFields.classList.remove('hidden');
                        if (previewBox) previewBox.classList.add('hidden');
                        if (toggleText) toggleText.innerText = 'Thu gọn';
                        if (toggleIcon) toggleIcon.classList.add('rotate-180');
                    }
                    return;
                }

                if (isPaymentSubmitting) {
                    e.preventDefault();
                    return false;
                }

                const isFree = @json($isFreeOrder);

                if (isFree) {
                    e.preventDefault();
                    isPaymentSubmitting = true;
                    payBtn.disabled = true;

                    // Hiển thị Spinner xoay trong suốt thời gian server gửi mail
                    if (typeof window.showProcessing === 'function') {
                        window.showProcessing('Đang cấp vé & gửi email...', 'Hệ thống đang xuất mã vé và gửi email xác nhận');
                    }

                    const formData = new FormData(paymentForm);

                    fetch(paymentForm.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    })
                    .then(async (response) => {
                        const data = await response.json();
                        if (!response.ok || !data.success) {
                            throw new Error(data.message || 'Lỗi đăng ký vé');
                        }
                        return data;
                    })
                    .then((data) => {
                        // Gửi mail và cấp vé xong -> Hiện tích xanh bung nở rồi redirect
                        if (typeof window.showSuccessAnimation === 'function') {
                            window.showSuccessAnimation('Đăng ký vé thành công!', () => {
                                window.location.href = data.redirect_url || '{{ route("orders.success", $order->order_code) }}';
                            });
                        } else {
                            window.location.href = data.redirect_url || '{{ route("orders.success", $order->order_code) }}';
                        }
                    })
                    .catch((error) => {
                        isPaymentSubmitting = false;
                        payBtn.disabled = false;
                        if (typeof window.hideProcessing === 'function') {
                            window.hideProcessing();
                        }
                        alert('Không thể nhận vé: ' + error.message);
                    });

                    return;
                }

                isPaymentSubmitting = true;
                payBtn.disabled = true;
                payBtn.classList.add('opacity-75', 'cursor-not-allowed');
                payBtn.innerHTML = `<span>Đang xử lý...</span>`;
            });
        }
    })();
</script>

{{-- Khối Style và Script điều khiển chung (chỉ nạp 1 lần duy nhất) --}}
@once
    <style>
        /* Hiệu ứng hiển thị modal mượt mà */
        .animate-fade-in-up {
            animation: fadeInUpModal 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes fadeInUpModal {
            from {
                opacity: 0;
                transform: translateY(10px) scale(0.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Thanh cuộn tinh tế cho modal */
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 9999px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 9999px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.25);
        }
    </style>

    <script>
        // Hàm mở modal thanh toán
        window.openCheckoutModal = function (orderId) {
            let modal = null;
            if (orderId) {
                modal = document.getElementById('checkout-modal-' + orderId)
                    || document.querySelector(`[data-order-code="${orderId}"]`);
            } else {
                modal = document.querySelector('.checkout-modal-backdrop');
            }

            if (modal) {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }
        };

        // Hàm đóng modal thanh toán
        window.closeCheckoutModal = function (orderId) {
            if (orderId) {
                const modal = document.getElementById('checkout-modal-' + orderId)
                    || document.querySelector(`[data-order-code="${orderId}"]`);
                if (modal) modal.classList.add('hidden');
            } else {
                document.querySelectorAll('.checkout-modal-backdrop').forEach(m => m.classList.add('hidden'));
            }
            document.body.classList.remove('overflow-hidden');
        };

        // Đóng modal khi nhấn phím Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                window.closeCheckoutModal();
            }
        });
    </script>
@endonce