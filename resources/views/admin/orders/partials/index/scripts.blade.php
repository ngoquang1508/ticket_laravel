@push('scripts')
    <script>
        // Mở modal xác nhận hủy đơn
        function openCancelModal(orderCode, actionUrl) {
            document.getElementById('modal-order-code').textContent = '#' + orderCode;
            document.getElementById('cancel-form').action = actionUrl;
            const modal = document.getElementById('cancel-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        // Đóng modal xác nhận hủy đơn
        function closeCancelModal() {
            const modal = document.getElementById('cancel-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Xử lý hoạt họa Spinner -> Tích xanh bằng fetch AJAX theo thời gian gửi mail thực tế khi admin duyệt nhanh
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.quick-confirm-form').forEach(form => {
                form.addEventListener('submit', function (e) {
                    const code = form.dataset.orderCode || '';
                    if (!confirm('Bạn có chắc chắn muốn xác nhận thanh toán cho đơn hàng #' + code + '?')) {
                        e.preventDefault();
                        return false;
                    }

                    e.preventDefault();
                    const btn = form.querySelector('button[type="submit"]');
                    if (btn) btn.disabled = true;

                    if (typeof window.showProcessing === 'function') {
                        window.showProcessing('Đang duyệt đơn & gửi email vé...', 'Hệ thống đang xuất vé và gửi email cho khách');
                    }

                    const formData = new FormData(form);

                    fetch(form.action, {
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
                                throw new Error(data.message || 'Lỗi xử lý xác nhận đơn');
                            }
                            return data;
                        })
                        .then((data) => {
                            if (typeof window.showSuccessAnimation === 'function') {
                                window.showSuccessAnimation('Duyệt đơn & Gửi email thành công!', 'Đang cập nhật danh sách...', () => {
                                    window.location.reload();
                                });
                            } else {
                                window.location.reload();
                            }
                        })
                        .catch((error) => {
                            if (btn) btn.disabled = false;
                            if (typeof window.hideProcessing === 'function') {
                                window.hideProcessing();
                            }
                            alert('Không thể duyệt đơn: ' + error.message);
                        });
                });
            });
        });
    </script>
@endpush
