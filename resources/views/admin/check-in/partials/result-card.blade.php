{{-- Card Kết quả kiểm tra vé --}}
<div id="result-card"
    class="rounded-2xl border border-gray-200/80 bg-white p-6 shadow-xs min-h-[380px] flex flex-col justify-between transition-all duration-300">

    {{-- Trạng thái chờ quét ban đầu --}}
    <div id="state-idle" class="flex-1 flex flex-col items-center justify-center text-center py-10">
        <div class="w-20 h-20 rounded-3xl bg-gray-100 border border-gray-200/80 flex items-center justify-center text-gray-400 mb-4">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
            </svg>
        </div>
        <h3 class="text-base font-bold text-gray-800">Sẵn sàng kiểm tra vé</h3>
        <p class="text-sm text-gray-500 mt-1.5 max-w-sm">Chọn sự kiện đang soát vé, sau đó đưa mã QR trước camera hoặc nhập mã vé để hiển thị thông tin.</p>
    </div>

    {{-- Trạng thái đang tải / Loading --}}
    <div id="state-loading" class="hidden flex-1 flex flex-col items-center justify-center text-center py-12">
        <div class="h-12 w-12 animate-spin rounded-full border-4 border-primary-500 border-t-transparent mb-4"></div>
        <h3 class="text-base font-bold text-gray-800">Đang tra cứu vé...</h3>
        <p class="text-xs text-gray-500 mt-1">Hệ thống đang kiểm tra tính hợp lệ và sự kiện của vé</p>
    </div>

    {{-- Trạng thái hiển thị thông tin vé (Valid / Already Used / Wrong Event / Invalid) --}}
    <div id="state-result" class="hidden flex-1 flex flex-col">

        {{-- Header trạng thái vé --}}
        <div id="result-status-header" class="rounded-xl p-4 mb-4 flex items-center gap-3">
            <div id="result-status-icon" class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"></div>
            <div class="min-w-0 flex-1">
                <span id="result-status-badge" class="inline-block text-xs font-bold uppercase tracking-wider mb-0.5"></span>
                <h3 id="result-status-title" class="text-sm sm:text-base font-bold leading-tight"></h3>
            </div>
        </div>

        {{-- Chi tiết vé --}}
        <div class="space-y-3.5 bg-gray-50/70 border border-gray-200/80 rounded-xl p-4 text-xs sm:text-sm">

            {{-- Sự kiện --}}
            <div class="pb-3 border-b border-gray-200/80">
                <span class="text-gray-500 block text-xs">Sự kiện trên vé:</span>
                <h4 id="ticket-event-name" class="font-bold text-gray-900 text-sm sm:text-base mt-0.5"></h4>
                <p id="ticket-event-time" class="text-gray-600 mt-0.5 text-xs flex items-center gap-1.5"></p>
                <p id="ticket-event-location" class="text-gray-500 text-xs mt-0.5 truncate"></p>
            </div>

            {{-- Thông tin vé & chỗ ngồi --}}
            <div class="grid grid-cols-2 gap-3 pb-3 border-b border-gray-200/80">
                <div>
                    <span class="text-gray-500 block text-xs">Mã vé:</span>
                    <span id="ticket-code-display" class="font-mono font-bold text-gray-900 tracking-wider"></span>
                </div>
                <div>
                    <span class="text-gray-500 block text-xs">Loại vé:</span>
                    <span id="ticket-type-name" class="font-semibold text-gray-900"></span>
                </div>
                <div>
                    <span class="text-gray-500 block text-xs">Vị trí / Ghế:</span>
                    <span id="ticket-seat-display" class="font-bold text-primary-600 text-sm"></span>
                </div>
                <div>
                    <span class="text-gray-500 block text-xs">Mã đơn hàng:</span>
                    <span id="ticket-order-code" class="font-mono text-gray-700"></span>
                </div>
            </div>

            {{-- Thông tin khách hàng --}}
            <div class="grid grid-cols-2 gap-2 text-xs">
                <div>
                    <span class="text-gray-500">Khách hàng:</span>
                    <span id="ticket-customer-name" class="font-semibold text-gray-900 block truncate"></span>
                </div>
                <div>
                    <span class="text-gray-500">Số điện thoại:</span>
                    <span id="ticket-customer-phone" class="font-semibold text-gray-900 block truncate"></span>
                </div>
            </div>

            {{-- Thông tin check-in trước đó (nếu đã check-in) --}}
            <div id="ticket-checked-in-info"
                class="hidden mt-2 p-3 rounded-lg bg-amber-50 border border-amber-200 text-amber-900 text-xs">
                <div class="flex items-center gap-1.5 font-bold mb-1">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Thời gian check-in trước đó:</span>
                </div>
                <p class="font-mono font-semibold" id="ticket-checked-in-time"></p>
                <p class="text-gray-600 mt-0.5">Người thực hiện: <span class="font-medium" id="ticket-checked-in-by"></span></p>
            </div>
        </div>

        {{-- Thông báo tự động tiếp tục quét --}}
        <div id="auto-resume-notice" class="hidden mt-3 text-center text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg py-2 animate-pulse">
            ✓ Check-in thành công! Tự chuyển về trạng thái quét tiếp theo trong <span id="auto-resume-countdown">2</span>s...
        </div>

        {{-- Nút hành động --}}
        <div class="mt-5 flex flex-col sm:flex-row gap-2.5">
            {{-- Nút Xác nhận Check-in (chỉ hiển thị khi vé valid) --}}
            <button type="button" id="btn-confirm-checkin" onclick="CheckInApp.confirmCheckin()"
                class="hidden flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-3 text-sm font-bold text-white shadow-md shadow-emerald-600/20 transition hover:bg-emerald-700 cursor-pointer">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
                <span>Xác nhận Check-in</span>
                <span class="hidden sm:inline-block ml-1 text-xs opacity-75 font-normal">(Phím Space)</span>
            </button>

            {{-- Nút Quét vé tiếp theo / Đặt lại --}}
            <button type="button" id="btn-next-scan" onclick="CheckInApp.ui.resetToIdle()"
                class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 cursor-pointer">
                <span>Quét vé tiếp theo</span>
                <span class="hidden sm:inline-block ml-1 text-xs opacity-60 font-normal">(Phím Esc)</span>
            </button>
        </div>
    </div>

</div>
