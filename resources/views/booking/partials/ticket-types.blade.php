{{-- Danh sách các loại vé tự do (General Admission) --}}
<div class="bg-[#1e1e1e] rounded-2xl border border-gray-800 shadow-xl p-6">
    <div class="flex flex-wrap items-center justify-between gap-2 mb-4 pb-3 border-b border-gray-800/80">
        <h3 class="text-xl font-bold text-green-500">Các loại vé</h3>

        {{-- Badge thông tin hạn mức mua vé của người dùng --}}
        @if(isset($remainingQuota))
            @if($remainingQuota <= 0)
                <span class="text-base text-orange-500">Đã đạt giới hạn {{ $maxQuota ?? 10 }} vé cho sự kiện
                    này</span>
            @else
                <span class="text-base text-green-500">Bạn còn có thể mua tối đa: <strong
                        class="text-green-400 font-bold">{{ $remainingQuota }}</strong>
                    vé</span>
            @endif
        @endif
    </div>
    <div class="space-y-4" id="ticket-types-container">
        @foreach($event->ticketTypes as $ticket)
            <div class="ticket-type-card bg-[#2b2b2b] rounded-xl p-4 border border-gray-700 flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition-colors duration-300"
                data-id="{{ $ticket->id }}" data-price="{{ $ticket->price }}" data-name="{{ $ticket->name }}">
                <div>
                    <h4 class="font-bold text-lg text-white uppercase">{{ $ticket->name }}</h4>
                    <div class="text-green-500 font-bold text-lg mt-1">
                        {{ number_format($ticket->price, 0, ',', '.') }} đ
                    </div>
                    <div class="text-sm text-gray-400 mt-1">
                        Còn lại: <span class="font-bold text-gray-300">{{ $ticket->quantity }}</span> vé
                    </div>
                </div>
                <div class="flex items-center gap-3 bg-[#1e1e1e] p-1.5 rounded-lg border border-gray-700 w-fit">
                    <button type="button"
                        class="w-8 h-8 rounded bg-gray-700 hover:bg-gray-600 flex items-center justify-center text-white font-bold transition cursor-pointer btn-minus"
                        data-id="{{ $ticket->id }}">-</button>
                    <span class="w-8 text-center font-bold text-lg quantity-display" id="qty-{{ $ticket->id }}">0</span>
                    <button type="button"
                        class="w-8 h-8 rounded bg-gray-700 hover:bg-gray-600 flex items-center justify-center text-white font-bold transition cursor-pointer btn-plus"
                        data-id="{{ $ticket->id }}">+</button>
                </div>
            </div>
        @endforeach
    </div>
</div>