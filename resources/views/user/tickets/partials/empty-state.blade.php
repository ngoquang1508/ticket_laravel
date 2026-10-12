{{-- Trạng thái danh sách rỗng --}}
<div
    class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-600 bg-[#2b2b2b] p-12 text-center shadow-sm">
    <svg class="h-8 w-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
            d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
    </svg>
    <p class="mt-4 text-sm text-gray-300 max-w-sm leading-relaxed">
        @if($status)
            Không có vé nào ở trạng thái này.
        @else
            Bạn chưa mua vé nào. Hãy khám phá các sự kiện hấp dẫn đang diễn ra nhé!
        @endif
    </p>
    @if(!$status)
        <a href="{{ route('home') }}"
            class="mt-4 rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 transition">
            Khám phá sự kiện
        </a>
    @else
        <a href="{{ route('user.tickets') }}"
            class="mt-4 text-sm font-semibold text-blue-600 hover:text-blue-700 transition">
            Xem tất cả vé
        </a>
    @endif
</div>
