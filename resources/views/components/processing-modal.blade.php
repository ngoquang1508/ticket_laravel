{{-- 
    Component: Processing Modal (Spinner -> Success Checkmark Animation)
    Sử dụng chung cho các hành động xử lý đơn hàng, đặt vé miễn phí và duyệt đơn Admin.
--}}
<div id="processing-overlay" 
    class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/60 backdrop-blur-xs transition-opacity duration-300">
    <div class="relative w-11/12 max-w-sm rounded-2xl bg-[#1e1e1e] border border-white/10 p-6 text-center shadow-2xl transition-all transform scale-95 duration-200"
        id="processing-card">
        
        {{-- Vùng hiển thị Icon hoạt họa --}}
        <div class="relative mx-auto mb-4 flex h-20 w-20 items-center justify-center">
            {{-- 1. Icon Spinner xoay --}}
            <div id="processing-spinner" class="absolute inset-0 flex items-center justify-center transition-all duration-300">
                <svg class="h-16 w-16 animate-spin text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>

            {{-- 2. Icon Tích xanh Bung nở (Animated Checkmark SVG) --}}
            <div id="processing-success-icon" class="absolute inset-0 flex items-center justify-center opacity-0 scale-50 transition-all duration-300 pointer-events-none">
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-emerald-500/10 border-2 border-emerald-500 shadow-lg shadow-emerald-500/30">
                    <svg class="h-9 w-9 text-emerald-400 stroke-dash-animate" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Tiêu đề và Mô tả trạng thái --}}
        <h3 id="processing-title" class="text-base font-bold text-white transition-colors duration-200">
            Đang xử lý...
        </h3>
        <p id="processing-subtitle" class="mt-1 text-xs text-gray-400">
            Vui lòng chờ trong giây lát
        </p>
    </div>
</div>

<style>
    /* Hoạt họa vẽ nét tích xanh */
    @keyframes drawCheckmark {
        0% {
            stroke-dashoffset: 40;
            opacity: 0;
        }
        50% {
            opacity: 1;
        }
        100% {
            stroke-dashoffset: 0;
            opacity: 1;
        }
    }

    .stroke-dash-animate path {
        stroke-dasharray: 40;
        stroke-dashoffset: 40;
    }

    #processing-overlay.is-success .stroke-dash-animate path {
        animation: drawCheckmark 0.4s cubic-bezier(0.65, 0, 0.45, 1) forwards;
    }
</style>

<script>
(function() {
    // API điều khiển hiệu ứng Processing -> Success toàn cục
    window.showProcessing = function(title = 'Đang xử lý...', subtitle = 'Vui lòng chờ trong giây lát') {
        const overlay = document.getElementById('processing-overlay');
        const spinner = document.getElementById('processing-spinner');
        const successIcon = document.getElementById('processing-success-icon');
        const card = document.getElementById('processing-card');
        const titleEl = document.getElementById('processing-title');
        const subEl = document.getElementById('processing-subtitle');

        if (!overlay) return;

        overlay.classList.remove('hidden', 'is-success');
        overlay.classList.add('flex');

        // Reset về trạng thái Spinner
        spinner.classList.remove('opacity-0', 'scale-50');
        spinner.classList.add('opacity-100', 'scale-100');
        successIcon.classList.remove('opacity-100', 'scale-100');
        successIcon.classList.add('opacity-0', 'scale-50');

        titleEl.textContent = title;
        titleEl.className = 'text-base font-bold text-white';
        subEl.textContent = subtitle;

        setTimeout(() => {
            card.classList.remove('scale-95');
            card.classList.add('scale-100');
        }, 10);
    };

    window.showSuccessAnimation = function(title = 'Thành công!', subtitle = 'Đang chuyển tiếp...', onDone = null) {
        const overlay = document.getElementById('processing-overlay');
        const spinner = document.getElementById('processing-spinner');
        const successIcon = document.getElementById('processing-success-icon');
        const titleEl = document.getElementById('processing-title');
        const subEl = document.getElementById('processing-subtitle');

        if (!overlay) {
            if (typeof onDone === 'function') onDone();
            return;
        }

        // Chuyển sang trạng thái Tích Xanh
        overlay.classList.add('is-success');

        spinner.classList.remove('opacity-100', 'scale-100');
        spinner.classList.add('opacity-0', 'scale-50');

        successIcon.classList.remove('opacity-0', 'scale-50');
        successIcon.classList.add('opacity-100', 'scale-100');

        titleEl.textContent = title;
        titleEl.className = 'text-base font-bold text-emerald-400';
        subEl.textContent = subtitle;

        // Đợi hiệu ứng vẽ tích xanh chạy xong (700ms) rồi gọi callback hoàn tất
        setTimeout(() => {
            if (typeof onDone === 'function') {
                onDone();
            }
        }, 750);
    };

    window.hideProcessing = function() {
        const overlay = document.getElementById('processing-overlay');
        if (overlay) {
            overlay.classList.add('hidden');
            overlay.classList.remove('flex', 'is-success');
        }
    };
})();
</script>
