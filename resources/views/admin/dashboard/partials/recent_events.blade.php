{{-- Danh sách sự kiện sắp diễn ra / gần đây --}}
<section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
    <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
        <div>
            <h2 class="font-bold text-gray-900">Sự kiện gần đây & sắp diễn ra</h2>
            <p class="text-xs text-gray-500">Các sự kiện mới nhất trên hệ thống</p>
        </div>
        <a href="{{ route('admin.events.index') }}"
            class="text-xs font-semibold text-primary-600 hover:text-primary-700 hover:underline">
            Xem tất cả →
        </a>
    </div>
    <div class="divide-y divide-gray-100">
        @forelse($upcomingEvents as $event)
            <a href="{{ route('admin.events.show', $event) }}"
                class="flex items-center justify-between gap-4 px-5 py-3.5 transition hover:bg-gray-50/80">
                <div class="min-w-0">
                    <p class="truncate font-semibold text-gray-900">{{ $event->name }}</p>
                    <p class="mt-0.5 text-xs text-gray-500">
                        {{ $event->location?->name ?? 'Chưa gán địa điểm' }} ·
                        {{ $event->starts_at ? $event->starts_at->format('d/m/Y H:i') : 'Chưa định ngày' }}
                    </p>
                </div>
                <span
                    class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium {{ $event->is_published ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/60' : 'bg-gray-100 text-gray-600' }}">
                    <span
                        class="h-1.5 w-1.5 rounded-full {{ $event->is_published ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                    {{ $event->is_published ? 'Đã đăng' : 'Bản nháp' }}
                </span>
            </a>
        @empty
            <div class="px-5 py-10 text-center text-sm text-gray-500">
                Chưa có sự kiện nào trong hệ thống.
            </div>
        @endforelse
    </div>
</section>
