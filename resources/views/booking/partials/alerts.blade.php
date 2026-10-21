{{-- Thông báo lỗi từ session --}}
@if(session('error'))
    <div class="mb-4 bg-red-500/10 border border-red-500/50 text-red-500 px-4 py-3 rounded-xl font-medium flex items-center gap-2">
        <x-icons.exclamation class="w-5 h-5 shrink-0" />
        <span>{{ session('error') }}</span>
    </div>
@endif

{{-- Thông báo thông tin từ session --}}
@if(session('info'))
    <div class="mb-4 bg-blue-500/10 border border-blue-500/50 text-blue-400 px-4 py-3 rounded-xl font-medium flex items-center gap-2">
        <x-icons.info class="w-5 h-5 shrink-0" />
        <span>{{ session('info') }}</span>
    </div>
@endif

{{-- Thông báo thành công từ session --}}
@if(session('success'))
    <div class="mb-4 bg-green-500/10 border border-green-500/50 text-green-500 px-4 py-3 rounded-xl font-medium flex items-center gap-2">
        <x-icons.check class="w-5 h-5 shrink-0" />
        <span>{{ session('success') }}</span>
    </div>
@endif
