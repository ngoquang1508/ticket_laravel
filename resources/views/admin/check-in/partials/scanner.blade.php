{{-- Khối Camera Scanner --}}
<div class="overflow-hidden rounded-2xl border border-gray-200/80 bg-white shadow-xs">
    <div class="border-b border-gray-100 bg-gray-50/60 px-5 py-3.5 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <svg class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <h2 class="text-sm font-bold text-gray-900">Camera quét mã QR</h2>
        </div>

        {{-- Nút Bật/Tắt Camera & Chọn nguồn camera --}}
        <div class="flex items-center gap-2">
            <select id="camera-select"
                class="hidden rounded-lg border border-gray-200 bg-white px-2.5 py-1 text-xs font-medium text-gray-700 focus:border-primary-500 focus:ring-primary-500">
            </select>
            <button type="button" id="btn-toggle-camera"
                class="inline-flex items-center gap-1.5 rounded-lg bg-gray-900 px-3 py-1.5 text-xs font-semibold text-white shadow-xs transition hover:bg-gray-800 cursor-pointer">
                <span id="camera-status-dot" class="h-2 w-2 rounded-full bg-emerald-400"></span>
                <span id="btn-toggle-camera-text">Đang bật</span>
            </button>
        </div>
    </div>

    <div class="p-5 flex flex-col items-center">
        {{-- Khung view camera html5-qrcode (Căn giữa ở mọi kích thước màn hình) --}}
        <div class="relative mx-auto w-full max-w-[380px] aspect-square overflow-hidden rounded-2xl bg-gray-900 flex items-center justify-center border border-gray-800 shadow-inner">
            <div id="qr-reader" class="w-full h-full"></div>

            {{-- Placeholder khi camera chưa bật hoặc đang cấp quyền --}}
            <div id="camera-placeholder"
                class="absolute inset-0 flex flex-col items-center justify-center text-center p-6 bg-gray-900 text-gray-400 transition-opacity">
                <div class="w-16 h-16 rounded-2xl bg-gray-800/80 border border-gray-700 flex items-center justify-center text-gray-400 mb-3 shadow-inner">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </div>
                <p id="camera-placeholder-title" class="text-sm font-semibold text-gray-200">Đang khởi động camera...</p>
                <p id="camera-placeholder-desc" class="text-xs text-gray-400 mt-1 max-w-xs">Hệ thống đang tự động bật camera để quét mã QR. Vui lòng cho phép quyền truy cập camera nếu được hỏi.</p>
                <button type="button" id="btn-manual-start-cam" onclick="CheckInApp.camera.start()"
                    class="hidden mt-3 inline-flex items-center gap-1.5 rounded-lg bg-primary-600 px-3.5 py-1.5 text-xs font-semibold text-white hover:bg-primary-700 transition cursor-pointer">
                    <span>Bật lại Camera</span>
                </button>
            </div>
        </div>

        <div class="mt-3.5 flex items-center justify-between text-xs text-gray-500 w-full max-w-[380px] mx-auto px-1">
            <span>Phím tắt: <kbd class="px-1.5 py-0.5 rounded bg-gray-100 font-mono text-[10px] text-gray-700 border">C</kbd> (Bật/Tắt cam)</span>
            <span id="scan-hint" class="font-medium text-emerald-600 hidden">● Đang quét...</span>
        </div>
    </div>
</div>

{{-- Khối Nhập mã vé thủ công --}}
<div class="rounded-2xl border border-gray-200/80 bg-white p-5 shadow-xs">
    <div class="flex items-center gap-2 mb-3">
        <svg class="h-4 w-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
        </svg>
        <h2 class="text-sm font-bold text-gray-900">Nhập mã vé thủ công</h2>
    </div>
    <p class="text-xs text-gray-500 mb-3.5">Trong trường hợp mã QR bị mờ hoặc không dùng camera, hãy nhập mã vé in trên vé.</p>

    <form id="manual-ticket-form" onsubmit="event.preventDefault(); CheckInApp.handleManualSubmit();" class="flex gap-2">
        <div class="relative flex-1">
            <input type="text" id="manual-ticket-input" placeholder="Ví dụ: TCK-8X9Y2Z hoặc mã vé..."
                class="w-full rounded-xl border border-gray-300 bg-gray-50/50 px-4 py-2.5 font-mono text-sm uppercase tracking-wider text-gray-900 placeholder-gray-400 focus:border-primary-500 focus:bg-white focus:ring-2 focus:ring-primary-500/20"
                autocomplete="off">
        </div>
        <button type="submit" id="btn-manual-verify"
            class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white shadow-xs transition hover:bg-primary-700 cursor-pointer">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <span>Kiểm tra</span>
        </button>
    </form>
</div>

{{-- CSS tùy chỉnh cho video của thư viện Html5Qrcode --}}
<style>
    #qr-reader {
        border: none !important;
        width: 100% !important;
        height: 100% !important;
    }
    #qr-reader video {
        object-fit: cover !important;
        width: 100% !important;
        height: 100% !important;
        border-radius: 1rem;
    }
    #qr-reader__scan_region {
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
