{{-- Danh sách các vé vừa check-in gần nhất --}}
<div class="rounded-2xl border border-gray-200/80 bg-white p-5 shadow-xs">
    <div class="flex items-center justify-between mb-3 border-b border-gray-100 pb-3">
        <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
            <x-icons.clock class="w-4 h-4 text-emerald-600" />
            <span>Lịch sử check-in gần đây</span>
        </h3>
        <span class="text-xs text-gray-400">Tự động cập nhật</span>
    </div>

    <div id="recent-list" class="space-y-2.5 max-h-[220px] overflow-y-auto pr-1">
        @forelse($recentCheckins as $item)
            <div class="flex items-center justify-between p-2.5 rounded-xl bg-gray-50 border border-gray-200/70 text-xs">
                <div class="min-w-0 pr-2">
                    <span class="font-mono font-bold text-gray-900">{{ $item->ticket_code }}</span>
                    <span class="text-gray-500 block truncate">{{ $item->order->customer_name ?: ($item->order->user->name ?? 'Khách') }}
                        - {{ $item->order->event->name ?? '' }}</span>
                </div>
                <div class="text-right shrink-0">
                    <span class="text-emerald-700 font-semibold block">{{ $item->checked_in_at ? $item->checked_in_at->format('H:i:s') : '' }}</span>
                    <span class="text-[10px] text-gray-400">{{ $item->checked_in_at ? $item->checked_in_at->format('d/m') : '' }}</span>
                </div>
            </div>
        @empty
            <div id="recent-empty-hint" class="text-center py-6 text-gray-400 text-xs">
                Chưa có vé nào được check-in trong phiên làm việc này.
            </div>
        @endforelse
    </div>
</div>
