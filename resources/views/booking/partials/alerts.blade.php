{{-- Thông báo lỗi từ session --}}
@if(session('error'))
    <div class="mb-4 bg-red-500/10 border border-red-500/50 text-red-500 px-4 py-3 rounded-xl font-medium flex items-center gap-2">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>{{ session('error') }}</span>
    </div>
@endif

{{-- Thông báo thông tin từ session --}}
@if(session('info'))
    <div class="mb-4 bg-blue-500/10 border border-blue-500/50 text-blue-400 px-4 py-3 rounded-xl font-medium flex items-center gap-2">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>{{ session('info') }}</span>
    </div>
@endif

{{-- Thông báo thành công từ session --}}
@if(session('success'))
    <div class="mb-4 bg-green-500/10 border border-green-500/50 text-green-500 px-4 py-3 rounded-xl font-medium flex items-center gap-2">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        <span>{{ session('success') }}</span>
    </div>
@endif
