@if (in_array($order->status, ['pending', 'processing']))
    @push('scripts')
        <script>
            // Mở modal xác nhận hủy đơn
            function openCancelModal() {
                const modal = document.getElementById('cancel-modal');
                if (modal) {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }
            }

            // Đóng modal xác nhận hủy đơn
            function closeCancelModal() {
                const modal = document.getElementById('cancel-modal');
                if (modal) {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
            }

            // Xử lý hoạt họa Spinner -> Tích xanh bằng fetch AJAX theo thời gian gửi mail thực tế khi duyệt đơn
            document.addEventListener('DOMContentLoaded', function () {
                const confirmForm = document.getElementById('admin-confirm-order-form');
                const confirmBtn = document.getElementById('btn-admin-confirm');
                let isSubmitting = false;

                if (confirmForm && confirmBtn) {
                    confirmForm.addEventListener('submit', function (e) {
                        if (isSubmitting) {
                            e.preventDefault();
                            return false;
                        }

                        if (!confirm('Xác nhận đã nhận đủ tiền thanh toán cho đơn hàng #{{ $order->order_code }}?')) {
                            e.preventDefault();
                            return false;
                        }

                        e.preventDefault();
                        isSubmitting = true;
                        confirmBtn.disabled = true;

                        if (typeof window.showProcessing === 'function') {
                            window.showProcessing('Đang duyệt đơn & gửi email vé...', 'Hệ thống đang xuất mã vé và gửi email xác nhận');
                        }

                        // Gửi request AJAX thực tế tới Server
                        const formData = new FormData(confirmForm);

                        fetch(confirmForm.action, {
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
                            // Mail đã gửi xong -> Kích hoạt Tích xanh bung nở rồi tải lại trang
                            if (typeof window.showSuccessAnimation === 'function') {
                                window.showSuccessAnimation('Duyệt đơn & Gửi email thành công!', 'Đang tải lại thông tin...', () => {
                                    window.location.reload();
                                });
                            } else {
                                window.location.reload();
                            }
                        })
                        .catch((error) => {
                            isSubmitting = false;
                            confirmBtn.disabled = false;
                            if (typeof window.hideProcessing === 'function') {
                                window.hideProcessing();
                            }
                            alert('Không thể duyệt đơn: ' + error.message);
                        });
                    });
                }
            });
        </script>
    @endpush
@endif
