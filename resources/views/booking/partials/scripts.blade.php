{{-- Style định dạng thanh cuộn và khoảng đệm an toàn trên thiết bị di động --}}
<style>
    .pb-safe {
        padding-bottom: env(safe-area-inset-bottom, 16px);
    }

    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #4b5563;
        border-radius: 10px;
    }
</style>

{{-- Script khởi tạo sơ đồ ghế Konva nếu sự kiện có cấu hình bản đồ --}}
@if($hasMap)
    <script>
        let mapInstance = null;
        document.addEventListener('DOMContentLoaded', () => {
            let layout = @json($layout);
            if (typeof layout === 'string') {
                try { layout = JSON.parse(layout); } catch (e) { }
            }

            const isAssignedSeat = @json($isAssignedSeat);
            const soldSeats = @json($soldSeats ?? []);
            const heldSeats = @json($heldSeats ?? []);

            const initialSeatStates = {};
            soldSeats.forEach(id => initialSeatStates[id] = 'sold');
            heldSeats.forEach(id => initialSeatStates[id] = 'held');

            mapInstance = new SeatMapKonva({
                containerId: 'seat-map-canvas',
                layout: layout,
                mode: 'view',
                isAssignedSeat: isAssignedSeat,
                seatStates: initialSeatStates,
                onSeatClick: (seatId, obj) => {
                    if (typeof window.toggleSeatSelection === 'function') {
                        const mockEl = {
                            dataset: {
                                id: seatId,
                                zone: obj.name,
                                price: obj.price || 0,
                                ticketId: obj.ticket_type_id || 0
                            },
                            classList: {
                                add: () => mapInstance.updateSeatState(seatId, 'selected'),
                                remove: () => mapInstance.updateSeatState(seatId, 'available')
                            }
                        };
                        window.toggleSeatSelection(mockEl);
                    }
                },
                onZoneClick: (obj) => {
                    if (mapInstance) mapInstance.highlightZone(obj.name);
                    const card = document.querySelector(`.ticket-type-card[data-id="${obj.ticket_type_id}"]`);
                    if (card) {
                        card.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        card.classList.add('border-blue-500', 'bg-[#223344]');
                        setTimeout(() => card.classList.remove('border-blue-500', 'bg-[#223344]'), 1500);
                    }
                }
            });

            document.getElementById('zoom-in')?.addEventListener('click', () => mapInstance.zoomIn());
            document.getElementById('zoom-out')?.addEventListener('click', () => mapInstance.zoomOut());
            document.getElementById('zoom-reset')?.addEventListener('click', () => mapInstance.fitToScreen());
        });
    </script>
@endif

{{-- Script điều khiển giỏ vé, cập nhật giao diện và kiểm tra chống spam thanh toán --}}
<script>
    const isAssignedSeat = @json($isAssignedSeat);
    let cart = {}; // Cấu trúc: { seatId: { name, price, ticket_id } } đối với ghế cố định, { ticket_id: qty } đối với vé tự do

    function updateCartUI() {
        const list = document.getElementById('selected-tickets-list');
        const emptyMsg = document.getElementById('empty-cart-msg');
        const totalEl = document.getElementById('total-price');
        const mTotalEl = document.getElementById('mobile-total-price');
        const mCountEl = document.getElementById('mobile-ticket-count');
        const btn = document.getElementById('btn-checkout');
        const mBtn = document.getElementById('mobile-btn-checkout');
        const dataInput = document.getElementById('cart-data-input');

        // Xóa danh sách hiển thị cũ
        list.querySelectorAll('.cart-item').forEach(el => el.remove());

        let total = 0;
        let count = 0;

        if (isAssignedSeat) {
            const seats = Object.values(cart);
            count = seats.length;
            if (count > 0) {
                emptyMsg.style.display = 'none';
                seats.forEach(seat => {
                    total += seat.price;
                    list.insertAdjacentHTML('beforeend', `
                        <div class="cart-item bg-[#2b2b2b] p-4 rounded-xl border border-gray-700 mb-3 flex justify-between items-center transition">
                            <div>
                                <div class="font-bold text-white text-lg">${seat.id}</div>
                                <div class="text-sm text-gray-400">Khu ${seat.zone}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-green-500">${new Intl.NumberFormat('vi-VN').format(seat.price)} đ</div>
                                <button type="button" class="text-red-400 text-xs mt-1 hover:text-red-300 font-semibold cursor-pointer" onclick="removeSeat('${seat.id}')">Xóa</button>
                            </div>
                        </div>
                    `);
                });
            } else {
                emptyMsg.style.display = 'block';
            }
        } else {
            const ticketTypes = @json($event->ticketTypes->keyBy('id'));
            count = Object.values(cart).reduce((a, b) => a + b, 0);
            if (count > 0) {
                emptyMsg.style.display = 'none';
                Object.entries(cart).forEach(([id, qty]) => {
                    if (qty > 0) {
                        const ticket = ticketTypes[id];
                        const subtotal = ticket.price * qty;
                        total += subtotal;
                        list.insertAdjacentHTML('beforeend', `
                            <div class="cart-item bg-[#2b2b2b] p-4 rounded-xl border border-gray-700 mb-3 flex justify-between items-center transition">
                                <div>
                                    <div class="font-bold text-white">${ticket.name}</div>
                                    <div class="text-sm text-gray-400">${qty} vé</div>
                                </div>
                                <div class="text-right">
                                    <div class="font-bold text-green-500">${new Intl.NumberFormat('vi-VN').format(subtotal)} đ</div>
                                </div>
                            </div>
                        `);
                    }
                });
            } else {
                emptyMsg.style.display = 'block';
            }
        }

        const formattedTotal = new Intl.NumberFormat('vi-VN').format(total) + ' đ';
        if (totalEl) totalEl.textContent = formattedTotal;
        if (mTotalEl) mTotalEl.textContent = formattedTotal;
        if (mCountEl) mCountEl.textContent = `${count} vé`;
        if (dataInput) dataInput.value = JSON.stringify(cart);

        if (isSubmitting) return;

        const canCheckout = count > 0;
        [btn, mBtn].forEach(b => {
            if (b) {
                b.disabled = !canCheckout;
                if (canCheckout) {
                    b.classList.remove('bg-gray-500', 'text-[#121212]', 'cursor-not-allowed');
                    b.classList.add('bg-[#1db954]', 'text-white', 'hover:bg-white', 'hover:text-black');
                } else {
                    b.classList.add('bg-gray-500', 'text-[#121212]', 'cursor-not-allowed');
                    b.classList.remove('bg-[#1db954]', 'text-white', 'hover:bg-white', 'hover:text-black');
                }
            }
        });
    }

    // Xóa ghế đã chọn
    window.removeSeat = function (seatId) {
        delete cart[seatId];
        if (mapInstance) {
            mapInstance.updateSeatState(seatId, 'available');
        }
        updateCartUI();
    };

    // Chọn hoặc bỏ chọn ghế trên sơ đồ
    window.toggleSeatSelection = function (seatEl) {
        const id = seatEl.dataset.id;
        if (cart[id]) {
            delete cart[id];
            seatEl.classList.remove('status-selected');
        } else {
            cart[id] = {
                id: id,
                zone: seatEl.dataset.zone,
                price: Number(seatEl.dataset.price),
                ticket_id: seatEl.dataset.ticketId
            };
            seatEl.classList.add('status-selected');
        }
        updateCartUI();
    };

    // Gắn sự kiện tăng giảm số lượng cho vé tự do
    if (!isAssignedSeat) {
        document.querySelectorAll('.btn-plus').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const id = e.target.dataset.id;
                cart[id] = (cart[id] || 0) + 1;
                const qtyEl = document.getElementById(`qty-${id}`);
                if (qtyEl) qtyEl.textContent = cart[id];
                updateCartUI();
            });
        });
        document.querySelectorAll('.btn-minus').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const id = e.target.dataset.id;
                if (cart[id] > 0) {
                    cart[id]--;
                    const qtyEl = document.getElementById(`qty-${id}`);
                    if (qtyEl) qtyEl.textContent = cart[id];
                    updateCartUI();
                }
            });
        });
    }

    // --- Chống spam submit & Kiểm tra đơn hàng giữ vé ---
    let isSubmitting = false;
    let lastToastTime = 0;
    const hasActiveOrder = @json(isset($activeOrder) && $activeOrder && $activeOrder->expires_at > now());
    const activeOrderCode = @json($activeOrder?->order_code);

    // Kích hoạt trạng thái loading cho nút thanh toán
    function setSubmittingState() {
        isSubmitting = true;
        const spinnerSvg = `
            <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        `;

        const btn = document.getElementById('btn-checkout');
        const mBtn = document.getElementById('mobile-btn-checkout');

        [btn, mBtn].forEach(b => {
            if (b) {
                b.disabled = true;
                b.classList.add('opacity-75', 'cursor-not-allowed');
                b.classList.remove('hover:bg-white', 'hover:text-black');
                b.innerHTML = `${spinnerSvg} <span>Đang xử lý...</span>`;
            }
        });
    }

    // Kiểm tra điều kiện trước khi submit
    function validateBeforeCheckout(e) {
        if (isSubmitting) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            return false;
        }

        if (hasActiveOrder) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            const now = Date.now();
            if (now - lastToastTime > 2500) {
                lastToastTime = now;
                if (typeof window.showAppToast === 'function') {
                    window.showAppToast(
                        'error',
                        'Đang có đơn giữ vé',
                        `Bạn đang có đơn hàng giữ vé #${activeOrderCode} cho sự kiện này. Vui lòng thanh toán hoặc hủy đơn trước khi đặt mới.`
                    );
                }
            }
            return false;
        }

        const count = isAssignedSeat 
            ? Object.keys(cart).length 
            : Object.values(cart).reduce((a, b) => a + b, 0);

        if (count === 0) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            return false;
        }

        setSubmittingState();
        return true;
    }

    document.getElementById('checkout-form')?.addEventListener('submit', (e) => {
        if (!validateBeforeCheckout(e)) {
            return false;
        }
    });

    document.getElementById('mobile-btn-checkout')?.addEventListener('click', (e) => {
        if (validateBeforeCheckout(e)) {
            document.getElementById('checkout-form').submit();
        }
    });
</script>

{{-- Tự động mở modal thanh toán khi có session active_checkout_order --}}
@if(session('active_checkout_order'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof window.openCheckoutModal === 'function') {
                window.openCheckoutModal("{{ session('active_checkout_order') }}");
            }
        });
    </script>
@endif
