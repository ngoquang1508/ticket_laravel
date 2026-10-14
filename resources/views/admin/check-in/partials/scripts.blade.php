{{-- Nhúng thư viện quét QR html5-qrcode qua CDN --}}
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
    /**
     * Module Quản lý Check-in vé cho Quản trị viên
     * Đóng gói logic UI, Camera, Âm thanh và API vào đối tượng CheckInApp
     */
    const CheckInApp = (() => {
        // State nội bộ
        let html5QrCode = null;
        let isCameraRunning = false;
        let currentTicketCode = null;
        let autoResumeTimer = null;
        let autoResumeInterval = null;
        let audioCtx = null;

        // 1. Module âm thanh phản hồi (Web Audio API)
        const sound = {
            getAudioContext() {
                if (!audioCtx) {
                    audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                }
                if (audioCtx.state === 'suspended') {
                    audioCtx.resume();
                }
                return audioCtx;
            },

            play(type = 'success') {
                try {
                    const ctx = this.getAudioContext();
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();

                    osc.connect(gain);
                    gain.connect(ctx.destination);

                    if (type === 'success') {
                        // Tiếng beep đôi ngân vui tai khi vé hợp lệ / check-in thành công
                        osc.type = 'sine';
                        osc.frequency.setValueAtTime(880, ctx.currentTime);
                        osc.frequency.setValueAtTime(1174.66, ctx.currentTime + 0.1);
                        gain.gain.setValueAtTime(0.15, ctx.currentTime);
                        gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.3);
                        osc.start();
                        osc.stop(ctx.currentTime + 0.3);
                    } else {
                        // Tiếng buzz trầm khi gặp vé lỗi / vé đã dùng / sai sự kiện
                        osc.type = 'sawtooth';
                        osc.frequency.setValueAtTime(200, ctx.currentTime);
                        gain.gain.setValueAtTime(0.2, ctx.currentTime);
                        gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.35);
                        osc.start();
                        osc.stop(ctx.currentTime + 0.35);
                    }
                } catch (e) {
                    // Trình duyệt không hỗ trợ AudioContext
                }
            }
        };

        // 2. Tiện ích Escape HTML chống XSS
        function escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        // 3. Module Camera Scanner
        const camera = {
            async initDevices() {
                try {
                    const devices = await Html5Qrcode.getCameras();
                    const select = document.getElementById('camera-select');
                    if (devices && devices.length > 0) {
                        select.innerHTML = '';
                        devices.forEach((device, index) => {
                            const opt = document.createElement('option');
                            opt.value = device.id;
                            opt.text = device.label || `Camera ${index + 1}`;
                            select.appendChild(opt);
                        });
                        if (devices.length > 1) {
                            select.classList.remove('hidden');
                        }
                    }
                } catch (err) {
                    console.warn("Không thể truy cập danh sách camera:", err);
                }
                // Tự động bật camera khi vào trang
                this.start();
            },

            start() {
                if (isCameraRunning) return;

                const placeholder = document.getElementById('camera-placeholder');
                const placeholderTitle = document.getElementById('camera-placeholder-title');
                const placeholderDesc = document.getElementById('camera-placeholder-desc');
                const btnManualStart = document.getElementById('btn-manual-start-cam');
                const statusDot = document.getElementById('camera-status-dot');
                const btnText = document.getElementById('btn-toggle-camera-text');
                const scanHint = document.getElementById('scan-hint');

                placeholderTitle.textContent = "Đang khởi động camera...";
                placeholderDesc.textContent = "Vui lòng cho phép quyền truy cập camera nếu trình duyệt yêu cầu...";
                btnManualStart.classList.add('hidden');

                try {
                    if (html5QrCode) {
                        try { html5QrCode.clear(); } catch (e) {}
                    }
                    html5QrCode = new Html5Qrcode("qr-reader");

                    const cameraSelect = document.getElementById('camera-select');
                    const cameraId = (cameraSelect && cameraSelect.value) ? cameraSelect.value : { facingMode: "environment" };

                    const config = {
                        fps: 12,
                        qrbox: { width: 250, height: 250 },
                        aspectRatio: 1.0
                    };

                    html5QrCode.start(
                        cameraId,
                        config,
                        (decodedText) => onScanSuccess(decodedText),
                        () => {} // Bỏ qua frame không có QR
                    ).then(() => {
                        isCameraRunning = true;
                        placeholder.classList.add('hidden');
                        statusDot.classList.remove('bg-red-400');
                        statusDot.classList.add('bg-emerald-400');
                        btnText.textContent = "Tắt Camera";
                        scanHint.classList.remove('hidden');
                    }).catch(err => {
                        console.warn("Lỗi khi tự khởi động camera:", err);
                        isCameraRunning = false;
                        placeholder.classList.remove('hidden');
                        placeholderTitle.textContent = "Camera chưa được bật";
                        placeholderDesc.textContent = "Không thể khởi động camera tự động. Vui lòng cấp quyền truy cập camera trong trình duyệt hoặc bấm nút bên dưới.";
                        btnManualStart.classList.remove('hidden');
                        statusDot.classList.remove('bg-emerald-400');
                        statusDot.classList.add('bg-red-400');
                        btnText.textContent = "Bật Camera";
                        scanHint.classList.add('hidden');
                    });
                } catch (e) {
                    console.error("Lỗi khởi tạo Html5Qrcode:", e);
                }
            },

            async stop() {
                const placeholder = document.getElementById('camera-placeholder');
                const placeholderTitle = document.getElementById('camera-placeholder-title');
                const placeholderDesc = document.getElementById('camera-placeholder-desc');
                const btnManualStart = document.getElementById('btn-manual-start-cam');
                const statusDot = document.getElementById('camera-status-dot');
                const btnText = document.getElementById('btn-toggle-camera-text');
                const scanHint = document.getElementById('scan-hint');

                if (html5QrCode && isCameraRunning) {
                    try {
                        await html5QrCode.stop();
                        html5QrCode.clear();
                    } catch (e) {}
                }
                isCameraRunning = false;
                placeholder.classList.remove('hidden');
                placeholderTitle.textContent = "Camera đang tắt";
                placeholderDesc.textContent = "Bấm nút 'Bật Camera' để tiếp tục quét mã QR vé của khách hàng";
                btnManualStart.classList.remove('hidden');
                statusDot.classList.remove('bg-emerald-400');
                statusDot.classList.add('bg-red-400');
                btnText.textContent = "Bật Camera";
                scanHint.classList.add('hidden');
            },

            async toggle() {
                if (isCameraRunning) {
                    await this.stop();
                } else {
                    this.start();
                }
            },

            pause() {
                if (html5QrCode && isCameraRunning) {
                    try { html5QrCode.pause(); } catch (e) {}
                }
            },

            resume() {
                if (html5QrCode && isCameraRunning) {
                    try {
                        html5QrCode.resume();
                    } catch (e) {
                        this.start();
                    }
                }
            }
        };

        // 4. Callback khi quét QR thành công
        function onScanSuccess(decodedText) {
            if (!decodedText) return;
            camera.pause();
            verifyTicket(decodedText);
        }

        // 5. Module UI & Render
        const ui = {
            showState(stateName) {
                const states = ['idle', 'loading', 'result'];
                states.forEach(s => {
                    const el = document.getElementById(`state-${s}`);
                    if (el) {
                        if (s === stateName) {
                            el.classList.remove('hidden');
                        } else {
                            el.classList.add('hidden');
                        }
                    }
                });
            },

            renderResult(res) {
                this.showState('result');

                const headerEl = document.getElementById('result-status-header');
                const iconEl = document.getElementById('result-status-icon');
                const badgeEl = document.getElementById('result-status-badge');
                const titleEl = document.getElementById('result-status-title');
                const btnConfirm = document.getElementById('btn-confirm-checkin');
                const checkedInInfo = document.getElementById('ticket-checked-in-info');
                const autoResumeNotice = document.getElementById('auto-resume-notice');

                headerEl.className = "rounded-xl p-4 mb-4 flex items-center gap-3";
                btnConfirm.classList.add('hidden');
                checkedInInfo.classList.add('hidden');
                autoResumeNotice.classList.add('hidden');

                const ticket = res.ticket;

                if (res.status === 'valid') {
                    // Vé hợp lệ
                    sound.play('success');
                    headerEl.classList.add('bg-emerald-50', 'border', 'border-emerald-200', 'text-emerald-900');
                    iconEl.className = "w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0";
                    iconEl.innerHTML = `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>`;
                    badgeEl.className = "inline-block text-xs font-bold uppercase tracking-wider text-emerald-700";
                    badgeEl.textContent = "Vé hợp lệ";
                    titleEl.textContent = "Đúng sự kiện - Sẵn sàng check-in!";

                    btnConfirm.classList.remove('hidden');
                    btnConfirm.focus();
                    this.fillTicketDetails(ticket);
                } else if (res.status === 'already_used') {
                    // Vé đã sử dụng trước đó
                    sound.play('error');
                    headerEl.classList.add('bg-amber-50', 'border', 'border-amber-200', 'text-amber-900');
                    iconEl.className = "w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0";
                    iconEl.innerHTML = `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>`;
                    badgeEl.className = "inline-block text-xs font-bold uppercase tracking-wider text-amber-700";
                    badgeEl.textContent = "Vé đã sử dụng";
                    titleEl.textContent = res.message || "Vé này đã được check-in trước đó!";

                    this.fillTicketDetails(ticket);
                    checkedInInfo.classList.remove('hidden');
                    document.getElementById('ticket-checked-in-time').textContent = ticket?.checked_in_at || 'Không rõ thời gian';
                    document.getElementById('ticket-checked-in-by').textContent = ticket?.checked_in_by_name || 'Admin';

                    this.resumeCameraAfterDelay(4000);
                } else if (res.status === 'wrong_event') {
                    // Vé sai sự kiện
                    sound.play('error');
                    headerEl.classList.add('bg-rose-50', 'border', 'border-rose-200', 'text-rose-900');
                    iconEl.className = "w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0";
                    iconEl.innerHTML = `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>`;
                    badgeEl.className = "inline-block text-xs font-bold uppercase tracking-wider text-rose-700";
                    badgeEl.textContent = "Sai sự kiện";
                    titleEl.textContent = res.message || "Vé không thuộc sự kiện đang chọn check-in!";

                    if (ticket) this.fillTicketDetails(ticket);
                    this.resumeCameraAfterDelay(4000);
                } else {
                    // Lỗi khác (không tìm thấy vé, đơn hủy, lỗi server)
                    sound.play('error');
                    headerEl.classList.add('bg-red-50', 'border', 'border-red-200', 'text-red-900');
                    iconEl.className = "w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center shrink-0";
                    iconEl.innerHTML = `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>`;
                    badgeEl.className = "inline-block text-xs font-bold uppercase tracking-wider text-red-700";
                    badgeEl.textContent = "Không hợp lệ";
                    titleEl.textContent = res.message || "Vé không hợp lệ!";

                    if (ticket) {
                        this.fillTicketDetails(ticket);
                    } else {
                        document.getElementById('ticket-code-display').textContent = currentTicketCode || '—';
                        document.getElementById('ticket-event-name').textContent = "Không có thông tin";
                        document.getElementById('ticket-event-time').textContent = "—";
                        document.getElementById('ticket-event-location').textContent = "—";
                        document.getElementById('ticket-type-name').textContent = "—";
                        document.getElementById('ticket-seat-display').textContent = "—";
                        document.getElementById('ticket-order-code').textContent = "—";
                        document.getElementById('ticket-customer-name').textContent = "—";
                        document.getElementById('ticket-customer-phone').textContent = "—";
                    }
                    this.resumeCameraAfterDelay(4000);
                }
            },

            fillTicketDetails(ticket) {
                if (!ticket) return;

                document.getElementById('ticket-event-name').textContent = ticket.event_name || 'Sự kiện';
                document.getElementById('ticket-event-time').textContent = ticket.event_time ? `🕒 ${ticket.event_time}` : '';
                document.getElementById('ticket-event-location').textContent = ticket.event_location ? `📍 ${ticket.event_location} (${ticket.event_address})` : '';
                document.getElementById('ticket-code-display').textContent = ticket.ticket_code;
                document.getElementById('ticket-type-name').textContent = ticket.ticket_type_name;

                let seatInfo = ticket.seat_number ? `Ghế ${ticket.seat_number}` : 'Vé tự do';
                if (ticket.zone) seatInfo += ` (${ticket.zone})`;
                document.getElementById('ticket-seat-display').textContent = seatInfo;

                document.getElementById('ticket-order-code').textContent = `#${ticket.order_code}`;
                document.getElementById('ticket-customer-name').textContent = ticket.customer_name;
                document.getElementById('ticket-customer-phone').textContent = ticket.customer_phone || ticket.customer_email || '—';
            },

            resumeCameraAfterDelay(ms) {
                this.clearAutoResumeTimer();
                autoResumeTimer = setTimeout(() => {
                    camera.resume();
                }, ms);
            },

            startCountdown(seconds = 2) {
                this.clearAutoResumeTimer();

                const noticeEl = document.getElementById('auto-resume-notice');
                const countdownEl = document.getElementById('auto-resume-countdown');
                noticeEl.classList.remove('hidden');

                let remaining = seconds;
                countdownEl.textContent = remaining;

                autoResumeInterval = setInterval(() => {
                    remaining--;
                    if (remaining > 0) {
                        countdownEl.textContent = remaining;
                    } else {
                        this.clearAutoResumeTimer();
                        this.resetToIdle();
                    }
                }, 1000);
            },

            clearAutoResumeTimer() {
                if (autoResumeTimer) {
                    clearTimeout(autoResumeTimer);
                    autoResumeTimer = null;
                }
                if (autoResumeInterval) {
                    clearInterval(autoResumeInterval);
                    autoResumeInterval = null;
                }
                const noticeEl = document.getElementById('auto-resume-notice');
                if (noticeEl) noticeEl.classList.add('hidden');
            },

            resetToIdle() {
                this.clearAutoResumeTimer();
                this.showState('idle');

                document.getElementById('manual-ticket-input').value = '';
                currentTicketCode = null;

                const btnConfirm = document.getElementById('btn-confirm-checkin');
                btnConfirm.disabled = false;
                btnConfirm.classList.remove('opacity-75', 'cursor-not-allowed');

                camera.resume();
            },

            addRecentHistory(ticket) {
                if (!ticket) return;

                const container = document.getElementById('recent-list');
                const emptyHint = document.getElementById('recent-empty-hint');
                if (emptyHint) emptyHint.remove();

                const now = new Date();
                const timeStr = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0') + ':' + String(now.getSeconds()).padStart(2, '0');
                const dateStr = String(now.getDate()).padStart(2, '0') + '/' + String(now.getMonth() + 1).padStart(2, '0');

                const el = document.createElement('div');
                el.className = "flex items-center justify-between p-2.5 rounded-xl bg-emerald-50/70 border border-emerald-200 text-xs animate-fade-in";
                el.innerHTML = `
                    <div class="min-w-0 pr-2">
                        <span class="font-mono font-bold text-gray-900">${escapeHtml(ticket.ticket_code)}</span>
                        <span class="text-gray-500 block truncate">${escapeHtml(ticket.customer_name)} - ${escapeHtml(ticket.event_name)}</span>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="text-emerald-700 font-semibold block">${timeStr}</span>
                        <span class="text-[10px] text-gray-400">${dateStr}</span>
                    </div>
                `;

                container.insertBefore(el, container.firstChild);
            }
        };

        // 6. Xử lý Verify vé
        async function verifyTicket(code) {
            ui.clearAutoResumeTimer();

            const eventSelect = document.getElementById('event-select');
            const eventId = eventSelect.value;

            // Kiểm tra sự kiện đã chọn
            if (!eventId) {
                sound.play('error');
                document.getElementById('event-warning-hint').classList.remove('hidden');
                eventSelect.classList.add('border-red-500', 'ring-2', 'ring-red-200');
                eventSelect.focus();

                ui.renderResult({
                    status: 'no_event_selected',
                    message: 'Vui lòng chọn sự kiện bạn đang thực hiện check-in ở phía trên trước khi quét vé!'
                });

                setTimeout(() => camera.resume(), 2500);
                return;
            }

            currentTicketCode = code;
            ui.showState('loading');

            try {
                const response = await fetch("{{ route('admin.checkin.verify') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({
                        ticket_code: code,
                        event_id: eventId
                    })
                });

                const data = await response.json();
                ui.renderResult(data);
            } catch (error) {
                console.error("Lỗi kết nối kiểm tra vé:", error);
                ui.renderResult({
                    status: 'error',
                    message: 'Không thể kết nối đến máy chủ. Vui lòng kiểm tra lại kết nối mạng!'
                });
                setTimeout(() => camera.resume(), 3000);
            }
        }

        // 7. Xử lý Xác nhận Check-in vé
        async function confirmCheckin() {
            if (!currentTicketCode) return;

            const eventSelect = document.getElementById('event-select');
            const eventId = eventSelect.value;
            if (!eventId) {
                alert('Vui lòng chọn sự kiện trước khi xác nhận check-in!');
                return;
            }

            const btnConfirm = document.getElementById('btn-confirm-checkin');
            btnConfirm.disabled = true;
            btnConfirm.classList.add('opacity-75', 'cursor-not-allowed');

            try {
                const response = await fetch("{{ route('admin.checkin.confirm') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({
                        ticket_code: currentTicketCode,
                        event_id: eventId
                    })
                });

                const res = await response.json();

                if (res.success) {
                    sound.play('success');
                    if (window.showAppToast) {
                        window.showAppToast('success', 'Thành công', 'Đã check-in vé: ' + currentTicketCode);
                    }

                    // Cập nhật số đếm hôm nay
                    const statEl = document.getElementById('stat-today-count');
                    statEl.textContent = (parseInt(statEl.textContent.replace(/,/g, '')) + 1).toLocaleString('vi-VN');

                    // Thêm vào danh sách lịch sử
                    ui.addRecentHistory(res.ticket);

                    // Cập nhật header kết quả
                    const headerEl = document.getElementById('result-status-header');
                    headerEl.className = "rounded-xl p-4 mb-4 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-900";
                    document.getElementById('result-status-badge').textContent = "Đã check-in thành công";
                    document.getElementById('result-status-title').textContent = "Khách đã được xác nhận vào cổng!";
                    btnConfirm.classList.add('hidden');

                    // Tự động chuyển về trạng thái sẵn sàng sau 2 giây
                    ui.startCountdown(2);
                } else {
                    sound.play('error');
                    alert(res.message || 'Lỗi khi xác nhận check-in!');
                    btnConfirm.disabled = false;
                    btnConfirm.classList.remove('opacity-75', 'cursor-not-allowed');
                }
            } catch (error) {
                console.error("Lỗi khi gọi API confirm:", error);
                alert("Lỗi kết nối khi xác nhận check-in!");
                btnConfirm.disabled = false;
                btnConfirm.classList.remove('opacity-75', 'cursor-not-allowed');
            }
        }

        // 8. Xử lý nhập mã thủ công
        function handleManualSubmit() {
            const input = document.getElementById('manual-ticket-input');
            const code = input.value.trim();
            if (!code) {
                alert('Vui lòng nhập mã vé!');
                input.focus();
                return;
            }

            camera.pause();
            verifyTicket(code);
        }

        // 9. Phím tắt tiện ích (Keyboard Shortcuts)
        function initKeyboardShortcuts() {
            document.addEventListener('keydown', (e) => {
                // Không kích hoạt shortcut khi đang gõ text trong ô input
                if (['INPUT', 'SELECT', 'TEXTAREA'].includes(document.activeElement.tagName)) {
                    return;
                }

                // Phím Space: Xác nhận check-in khi nút đang hiển thị
                if (e.code === 'Space') {
                    const btnConfirm = document.getElementById('btn-confirm-checkin');
                    if (btnConfirm && !btnConfirm.classList.contains('hidden') && !btnConfirm.disabled) {
                        e.preventDefault();
                        confirmCheckin();
                    }
                }

                // Phím Escape: Hủy hoặc reset về trạng thái sẵn sàng
                if (e.key === 'Escape') {
                    ui.resetToIdle();
                }

                // Phím C: Bật / Tắt camera
                if (e.key.toLowerCase() === 'c' && !e.ctrlKey && !e.metaKey) {
                    e.preventDefault();
                    camera.toggle();
                }
            });
        }

        // 10. Khởi tạo selector sự kiện
        function initEventSelector() {
            const eventSelect = document.getElementById('event-select');
            const subtext = document.getElementById('event-info-subtext');
            const savedEventId = localStorage.getItem('admin_checkin_selected_event_id');

            function updateSubtext() {
                const opt = eventSelect.selectedOptions[0];
                if (opt && opt.value) {
                    const time = opt.dataset.time;
                    const loc = opt.dataset.location;
                    subtext.textContent = `Thời gian: ${time || '—'} | Địa điểm: ${loc || '—'}`;
                } else {
                    subtext.textContent = 'Hệ thống chỉ chấp nhận vé thuộc đúng sự kiện đang chọn';
                }
            }

            if (savedEventId) {
                const optionExists = Array.from(eventSelect.options).some(opt => opt.value === savedEventId);
                if (optionExists) {
                    eventSelect.value = savedEventId;
                    updateSubtext();
                }
            }

            eventSelect.addEventListener('change', () => {
                const val = eventSelect.value;
                if (val) {
                    localStorage.setItem('admin_checkin_selected_event_id', val);
                    document.getElementById('event-warning-hint').classList.add('hidden');
                    eventSelect.classList.remove('border-red-500', 'ring-2', 'ring-red-200');
                } else {
                    localStorage.removeItem('admin_checkin_selected_event_id');
                }
                updateSubtext();
            });
        }

        // Public API
        return {
            init() {
                initEventSelector();
                camera.initDevices();
                initKeyboardShortcuts();

                document.getElementById('btn-toggle-camera').addEventListener('click', () => camera.toggle());
            },
            camera,
            sound,
            ui,
            handleManualSubmit,
            verifyTicket,
            confirmCheckin
        };
    })();

    // Kích hoạt khi trang đã sẵn sàng
    document.addEventListener('DOMContentLoaded', () => {
        CheckInApp.init();
    });
</script>
