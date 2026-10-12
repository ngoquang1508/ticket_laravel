{{-- Script tương tác vé, modal và đếm ngược thời gian --}}
<script>
    // Mở / đóng dropdown danh sách vé
    function toggleOrderTickets(orderId) {
        const content = document.getElementById('ticket-details-' + orderId);
        const span = document.getElementById('btn-text-' + orderId);
        const icon = document.getElementById('btn-icon-' + orderId);
        if (!content) return;

        if (content.classList.contains('grid-rows-[0fr]')) {
            content.classList.remove('grid-rows-[0fr]', 'opacity-0');
            content.classList.add('grid-rows-[1fr]', 'opacity-100');
            if (span) span.innerText = 'Ẩn bớt';
            if (icon) icon.classList.add('rotate-180');
        } else {
            content.classList.remove('grid-rows-[1fr]', 'opacity-100');
            content.classList.add('grid-rows-[0fr]', 'opacity-0');
            if (span) span.innerText = 'Xem chi tiết vé';
            if (icon) icon.classList.remove('rotate-180');
        }
    }

    // Mở modal sửa vé
    function openEditTicketModal(orderId) {
        const modal = document.getElementById('edit-ticket-modal-' + orderId);
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    // Đóng modal sửa vé
    function closeEditTicketModal(orderId) {
        const modal = document.getElementById('edit-ticket-modal-' + orderId);
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    // Thay đổi số lượng vé trong modal
    function changeModalTicketQty(orderId, ticketTypeId, delta) {
        const input = document.getElementById(`qty-input-${orderId}-${ticketTypeId}`);
        if (!input) return;

        const maxAllowedForType = parseInt(input.dataset.max) || 10;
        const currentVal = parseInt(input.value) || 0;

        // Tính tổng số vé hiện tại của các loại vé khác trong đơn này
        const allInputs = document.querySelectorAll(`input[data-order="${orderId}"]`);
        let otherTotal = 0;
        allInputs.forEach(inp => {
            if (inp !== input) {
                otherTotal += parseInt(inp.value) || 0;
            }
        });

        let newVal = currentVal + delta;
        if (newVal < 0) newVal = 0;
        if (newVal > maxAllowedForType) newVal = maxAllowedForType;
        if (otherTotal + newVal > 10) {
            newVal = Math.max(0, 10 - otherTotal);
        }

        input.value = newVal;
        recalcModalSummary(orderId);
    }

    // Tính lại tổng số lượng và tổng tiền trong modal
    function recalcModalSummary(orderId) {
        const allInputs = document.querySelectorAll(`input[data-order="${orderId}"]`);
        let totalQty = 0;
        let totalPrice = 0;

        allInputs.forEach(inp => {
            const qty = parseInt(inp.value) || 0;
            const price = parseFloat(inp.dataset.price) || 0;
            totalQty += qty;
            totalPrice += qty * price;
        });

        const qtySpan = document.getElementById(`summary-qty-${orderId}`);
        const priceSpan = document.getElementById(`summary-price-${orderId}`);
        const submitBtn = document.getElementById(`submit-btn-${orderId}`);
        const errorMsg = document.getElementById(`error-msg-${orderId}`);

        if (qtySpan) qtySpan.textContent = totalQty;
        if (priceSpan) priceSpan.textContent = new Intl.NumberFormat('vi-VN').format(totalPrice) + ' ₫';

        if (submitBtn) {
            if (totalQty === 0) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                if (errorMsg) {
                    errorMsg.textContent = 'Vui lòng chọn ít nhất 1 vé. Nếu không muốn giữ vé nữa, hãy bấm Hủy đơn hàng.';
                    errorMsg.classList.remove('hidden');
                }
            } else if (totalQty > 10) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                if (errorMsg) {
                    errorMsg.textContent = 'Tối đa 10 vé trong một đơn hàng.';
                    errorMsg.classList.remove('hidden');
                }
            } else {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                if (errorMsg) {
                    errorMsg.classList.add('hidden');
                }
            }
        }
    }

    // Lắng nghe phím Escape để đóng modal đang mở
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('[id^="edit-ticket-modal-"]:not(.hidden)').forEach(modal => {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            });
        }
    });

    // Cuộn mượt đến thẻ đơn hàng nếu có hash trên URL
    document.addEventListener('DOMContentLoaded', () => {
        if (window.location.hash) {
            const targetOrder = document.querySelector(window.location.hash);
            if (targetOrder) {
                setTimeout(() => {
                    targetOrder.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 150);
            }
        }
    });

    // Script đếm ngược thời gian hết hạn đơn hàng theo thời gian thực
    document.addEventListener('DOMContentLoaded', () => {
        const countElements = document.querySelectorAll('[data-order-countdown]');
        if (countElements.length === 0) return;

        function updateCountdowns() {
            const now = new Date().getTime();
            countElements.forEach(el => {
                const expireTime = new Date(el.dataset.orderCountdown).getTime();
                const diff = expireTime - now;
                if (diff <= 0) {
                    el.textContent = 'Đã quá hạn giữ vé';
                    el.className = 'inline-flex items-center gap-1 font-semibold text-red-400 bg-red-500/20 px-2.5 py-1 rounded-full text-[11px] sm:text-xs';
                    return;
                }

                const hours = Math.floor(diff / (1000 * 60 * 60));
                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                if (hours > 0) {
                    el.textContent = `(Còn lại: ${hours} giờ ${minutes} phút)`;
                } else {
                    const secStr = seconds < 10 ? '0' + seconds : seconds;
                    const minStr = minutes < 10 ? '0' + minutes : minutes;
                    el.textContent = `(Còn lại: ${minStr}:${secStr})`;
                }
            });
        }

        updateCountdowns();
        setInterval(updateCountdowns, 1000);
    });
</script>
