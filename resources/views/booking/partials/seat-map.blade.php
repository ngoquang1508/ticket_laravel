{{-- Khung hiển thị sơ đồ ghế Konva SeatMap kèm thanh công cụ Zoom & Chú thích --}}
<div class="bg-[#1e1e1e] rounded-2xl border border-gray-800 shadow-xl overflow-hidden flex flex-col">
    <!-- Thanh điều khiển & Chú thích trạng thái ghế -->
    <div class="p-4 border-b border-gray-800 flex flex-wrap items-center justify-between gap-4 bg-[#252525]">
        <div class="flex items-center gap-4 text-xs font-medium">
            @if($isAssignedSeat)
                <div class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded bg-[#3f3f46]"></span> Trống</div>
                <div class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded bg-[#3b82f6]"></span> Đang chọn</div>
                <div class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded bg-[#f97316]"></span> Đang giữ</div>
                <div class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded bg-[#ef4444]"></span> Đã bán</div>

                @if(isset($remainingQuota))
                    <div class="h-4 w-px bg-gray-700 hidden sm:block"></div>
                    <div class="text-xs {{ $remainingQuota <= 0 ? 'text-red-400 font-bold' : 'text-gray-300' }}">
                        Hạn mức: <strong class="{{ $remainingQuota <= 0 ? 'text-red-400' : 'text-green-400' }}">{{ $remainingQuota }}</strong>/{{ $maxQuota ?? 10 }} ghế
                    </div>
                @endif
            @else
                <div class="flex items-center gap-1.5 text-gray-300">
                    <svg class="w-4 h-4 text-blue-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                    </svg>
                    <span>Chọn khu vực bên dưới để xem giá vé</span>
                </div>
            @endif
        </div>
        <div class="flex items-center gap-2">
            <button type="button" id="zoom-out"
                class="w-8 h-8 rounded bg-[#333] hover:bg-[#444] text-white flex items-center justify-center transition cursor-pointer"
                title="Thu nhỏ (Zoom Out)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                </svg>
            </button>
            <button type="button" id="zoom-in"
                class="w-8 h-8 rounded bg-[#333] hover:bg-[#444] text-white flex items-center justify-center transition cursor-pointer"
                title="Phóng to (Zoom In)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            </button>
            <button type="button" id="zoom-reset"
                class="px-3 h-8 rounded bg-[#333] hover:bg-[#444] text-white text-xs font-semibold flex items-center justify-center transition cursor-pointer"
                title="Mặc định">
                Reset
            </button>
        </div>
    </div>

    <!-- Khung Canvas Konva -->
    <div id="map-viewport" class="relative w-full overflow-hidden bg-[#121212] rounded-b-2xl h-[400px]"
        style="touch-action: none;">
        <div id="seat-map-canvas" class="w-full h-full outline-none"></div>
    </div>
</div>
