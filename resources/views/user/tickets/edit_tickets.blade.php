@extends('layouts.app')

@section('title', 'Chỉnh sửa vé - ' . $event->name)

@section('content')
    @php
        $layout = $event->seatMap?->layout;
        if (is_string($layout)) {
            $layout = json_decode($layout, true);
        }
        $hasMap = !empty($layout) && !empty($layout['objects']);
        $isAssignedSeat = true;
    @endphp

    <div class="min-h-screen text-white pt-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('error'))
                <div class="mb-4 bg-red-500/10 border border-red-500/50 text-red-500 px-4 py-3 rounded-xl font-medium flex items-center gap-2">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="mb-4 bg-green-500/10 border border-green-500/50 text-green-500 px-4 py-3 rounded-xl font-medium flex items-center gap-2">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Header -->
            <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="flex flex-wrap items-center gap-2.5 mb-1.5">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-500/15 px-3 py-1 text-xs font-semibold text-blue-400 ring-1 ring-inset ring-blue-500/30">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            Chỉnh sửa đơn hàng #{{ $order->order_code }}
                        </span>
                        <span class="inline-flex items-center gap-1 text-xs text-amber-400 bg-amber-500/10 px-2.5 py-1 rounded-full border border-amber-500/20">
                            Hạn thanh toán: {{ $order->expires_at->format('H:i - d/m/Y') }}
                        </span>
                    </div>
                    <h1 class="text-2xl font-bold text-white mb-1">{{ $event->name }}</h1>
                    <p class="text-gray-400 text-sm flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        {{ \Carbon\Carbon::parse($event->starts_at)->format('H:i, d \T\h\á\n\g m, Y') }}
                    </p>
                </div>
                
                <!-- Nút điều hướng -->
                <div class="flex items-center gap-2.5 self-start md:self-auto">
                    <a href="{{ route('user.tickets', array_filter(['status' => request('status', $order->status), 'page' => request('page')])) }}#order-{{ $order->id }}"
                        class="text-sm font-semibold text-gray-300 hover:text-white bg-[#2b2b2b] hover:bg-[#333] px-4 py-2 rounded-lg transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Vé của tôi
                    </a>
                    <a href="{{ route('events.show', $event->slug) }}"
                        class="text-sm font-semibold text-gray-300 hover:text-white bg-[#2b2b2b] hover:bg-[#333] px-4 py-2 rounded-lg transition">
                        Quay lại sự kiện
                    </a>
                </div>
            </div>

            <!-- Banner hướng dẫn -->
            <div class="mb-6 rounded-xl border border-blue-500/20 bg-blue-500/10 p-3.5 sm:p-4 text-xs sm:text-sm text-blue-300 flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div class="leading-relaxed">
                    Bạn đang thay đổi vị trí ghế cho đơn hàng này. Các ghế bạn đang giữ hiện được đánh dấu màu <span class="font-semibold text-blue-400">Xanh dương (Đang chọn)</span>. Bạn có thể nhấn vào để bỏ chọn hoặc chọn thêm các ghế trống khác (tối đa 10 ghế). Sau khi chọn xong, nhấn <strong>Cập nhật vé</strong> để lưu lại.
                </div>
            </div>

            <div class="flex flex-col lg:flex-row gap-6">
                <!-- Left: Seat Map -->
                <div class="w-full lg:w-1/2 flex flex-col gap-6">
                    @if($hasMap)
                        <!-- Seat Map Area -->
                        <div class="bg-[#1e1e1e] rounded-2xl border border-gray-800 shadow-xl overflow-hidden flex flex-col">
                            <!-- Map Controls & Legend -->
                            <div class="p-4 border-b border-gray-800 flex flex-wrap items-center justify-between gap-4 bg-[#252525]">
                                <div class="flex items-center gap-4 text-xs font-medium">
                                    <div class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded bg-[#3f3f46]"></span> Trống</div>
                                    <div class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded bg-[#3b82f6]"></span> Đang chọn</div>
                                    <div class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded bg-[#f97316]"></span> Đang giữ</div>
                                    <div class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded bg-[#ef4444]"></span> Đã bán</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="button" id="zoom-out"
                                        class="w-8 h-8 rounded bg-[#333] hover:bg-[#444] text-white flex items-center justify-center transition cursor-pointer"
                                        title="Thu nhỏ (Zoom Out)">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                        </svg>
                                    </button>
                                    <button type="button" id="zoom-in"
                                        class="w-8 h-8 rounded bg-[#333] hover:bg-[#444] text-white flex items-center justify-center transition cursor-pointer"
                                        title="Phóng to (Zoom In)">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </button>
                                    <button type="button" id="zoom-reset"
                                        class="px-3 h-8 rounded bg-[#333] hover:bg-[#444] text-white text-xs font-semibold flex items-center justify-center transition cursor-pointer"
                                        title="Mặc định">
                                        Reset
                                    </button>
                                </div>
                            </div>

                            <!-- Canvas Container (Pan/Zoom) -->
                            <div id="map-viewport" class="relative w-full overflow-hidden bg-[#121212] rounded-b-2xl h-[420px]"
                                style="touch-action: none;">
                                <div id="seat-map-canvas" class="w-full h-full outline-none"></div>
                            </div>
                        </div>
                    @else
                        <div class="bg-[#1e1e1e] rounded-2xl border border-gray-800 p-8 text-center text-gray-400">
                            Sự kiện chưa cấu hình sơ đồ ghế.
                        </div>
                    @endif
                </div>

                <!-- Right: Order Summary (Sticky) -->
                <div class="w-full lg:w-1/2">
                    <div class="bg-[#1e1e1e] rounded-2xl border border-gray-800 shadow-xl lg:sticky lg:top-24 flex flex-col max-h-[calc(100vh-120px)]">
                        <div class="p-6 border-b border-gray-800 flex items-center justify-between">
                            <h2 class="text-xl font-bold text-white">Vé của bạn</h2>
                            <span class="text-xs text-gray-400 font-medium">Tối đa 10 ghế</span>
                        </div>

                        <div class="p-6 overflow-y-auto flex-1 custom-scrollbar" id="selected-tickets-list">
                            <div class="text-gray-500 text-center italic py-8" id="empty-cart-msg">
                                Chưa có ghế nào được chọn. Vui lòng chọn ít nhất 1 ghế.
                            </div>
                            <!-- Items will be injected here via JS -->
                        </div>

                        <div class="p-6 border-t border-gray-800 bg-[#252525] rounded-b-2xl">
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-gray-400">Tổng tạm tính mới</span>
                                <span class="text-2xl font-bold text-green-500" id="total-price">0 đ</span>
                            </div>
                            <form id="checkout-form" action="{{ route('orders.update-assigned-seats', $order) }}" method="POST">
                                @csrf
                                <input type="hidden" name="cart_data" id="cart-data-input">
                                <input type="hidden" name="status" value="{{ request('status', $order->status) }}">
                                @if(request('page'))
                                    <input type="hidden" name="page" value="{{ request('page') }}">
                                @endif
                                <button type="submit" id="btn-checkout"
                                    class="w-full py-4 rounded-xl font-bold text-lg transition duration-300 text-white bg-[#1db954] hover:bg-emerald-500 cursor-pointer disabled:bg-gray-500 disabled:text-[#121212] disabled:cursor-not-allowed">
                                    Cập nhật vé
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Sticky Bottom Bar -->
    <div class="lg:hidden fixed bottom-0 left-0 w-full bg-[#1e1e1e] border-t border-gray-800 shadow-[0_-10px_20px_rgba(0,0,0,0.5)] z-50 p-4 pb-safe flex items-center justify-between gap-4">
        <div class="flex flex-col">
            <span class="text-xs text-gray-400" id="mobile-ticket-count">0 vé</span>
            <span class="text-lg font-bold text-green-500" id="mobile-total-price">0 đ</span>
        </div>
        <button type="button" id="mobile-btn-checkout"
            class="px-6 py-3 rounded-xl font-bold transition duration-300 text-white bg-[#1db954] hover:bg-emerald-500 cursor-pointer disabled:bg-gray-500 disabled:text-[#121212] disabled:cursor-not-allowed">
            Cập nhật
        </button>
    </div>

    <style>
        .pb-safe {
            padding-bottom: env(safe-area-inset-bottom, 16px);
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #4b5563;
            border-radius: 10px;
        }
    </style>

    @if($hasMap)
        <script>
            let mapInstance = null;
            document.addEventListener('DOMContentLoaded', () => {
                let layout = @json($layout);
                if (typeof layout === 'string') {
                    try { layout = JSON.parse(layout); } catch (e) { }
                }

                const isAssignedSeat = true;
                const soldSeats = @json($soldSeats ?? []);
                const heldSeats = @json($heldSeats ?? []);
                const currentSeatNumbers = @json($currentSeatNumbers ?? []);

                const initialSeatStates = {};
                soldSeats.forEach(id => initialSeatStates[id] = 'sold');
                heldSeats.forEach(id => initialSeatStates[id] = 'held');
                // Đánh dấu các ghế hiện tại của đơn hàng này là 'selected'
                currentSeatNumbers.forEach(id => initialSeatStates[id] = 'selected');

                mapInstance = new SeatMapKonva({
                    containerId: 'seat-map-canvas',
                    layout: layout,
                    mode: 'view',
                    isAssignedSeat: isAssignedSeat,
                    seatStates: initialSeatStates,
                    onSeatClick: (seatId, obj) => {
                        if (typeof window.toggleSeatSelection === 'function') {
                            const mockEl = {
                                dataset: {
                                    id: seatId,
                                    zone: obj.name,
                                    price: obj.price || 0,
                                    ticketId: obj.ticket_type_id || 0
                                },
                                classList: {
                                    add: () => mapInstance.updateSeatState(seatId, 'selected'),
                                    remove: () => mapInstance.updateSeatState(seatId, 'available')
                                }
                            };
                            window.toggleSeatSelection(mockEl);
                        }
                    },
                    onZoneClick: (obj) => {
                        if (mapInstance) mapInstance.highlightZone(obj.name);
                    }
                });

                document.getElementById('zoom-in')?.addEventListener('click', () => mapInstance.zoomIn());
                document.getElementById('zoom-out')?.addEventListener('click', () => mapInstance.zoomOut());
                document.getElementById('zoom-reset')?.addEventListener('click', () => mapInstance.fitToScreen());

                // Khởi tạo hiển thị danh sách vé ban đầu
                updateCartUI();
            });
        </script>
    @endif

    <script>
        // --- Cart & Checkout Logic cho Chỉnh sửa vé ---
        const isAssignedSeat = true;
        // Khởi tạo cart từ danh sách ghế đang giữ của đơn hàng
        let cart = @json($initialCart ?? []);

        function updateCartUI() {
            const list = document.getElementById('selected-tickets-list');
            const emptyMsg = document.getElementById('empty-cart-msg');
            const totalEl = document.getElementById('total-price');
            const mTotalEl = document.getElementById('mobile-total-price');
            const mCountEl = document.getElementById('mobile-ticket-count');
            const btn = document.getElementById('btn-checkout');
            const mBtn = document.getElementById('mobile-btn-checkout');
            const dataInput = document.getElementById('cart-data-input');

            // Xóa các item hiện tại
            list.querySelectorAll('.cart-item').forEach(el => el.remove());

            let total = 0;
            const seats = Object.values(cart);
            const count = seats.length;

            if (count > 0) {
                emptyMsg.style.display = 'none';
                seats.forEach(seat => {
                    total += seat.price;
                    list.insertAdjacentHTML('beforeend', `
                        <div class="cart-item bg-[#2b2b2b] p-4 rounded-xl border border-gray-700 mb-3 flex justify-between items-center transition">
                            <div>
                                <div class="font-bold text-white text-lg">${seat.id}</div>
                                <div class="text-sm text-gray-400">Khu ${seat.zone}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-green-500">${new Intl.NumberFormat('vi-VN').format(seat.price)} đ</div>
                                <button type="button" class="text-red-400 text-xs mt-1 hover:text-red-300 font-semibold cursor-pointer" onclick="removeSeat('${seat.id}')">Xóa</button>
                            </div>
                        </div>
                    `);
                });
            } else {
                emptyMsg.style.display = 'block';
            }

            const formattedTotal = new Intl.NumberFormat('vi-VN').format(total) + ' đ';
            totalEl.textContent = formattedTotal;
            mTotalEl.textContent = formattedTotal;
            mCountEl.textContent = `${count} vé`;

            dataInput.value = JSON.stringify(cart);

            const canCheckout = count > 0;
            [btn, mBtn].forEach(b => {
                if (b) {
                    b.disabled = !canCheckout;
                    if (canCheckout) {
                        b.classList.remove('bg-gray-500', 'text-[#121212]', 'cursor-not-allowed');
                        b.classList.add('bg-[#1db954]', 'text-white', 'hover:bg-emerald-500');
                    } else {
                        b.classList.add('bg-gray-500', 'text-[#121212]', 'cursor-not-allowed');
                        b.classList.remove('bg-[#1db954]', 'text-white', 'hover:bg-emerald-500');
                    }
                }
            });
        }

        window.removeSeat = function (seatId) {
            delete cart[seatId];
            if (mapInstance) {
                mapInstance.updateSeatState(seatId, 'available');
            }
            updateCartUI();
        };

        window.toggleSeatSelection = function (seatEl) {
            const id = seatEl.dataset.id;
            if (cart[id]) {
                delete cart[id];
                if (mapInstance) {
                    mapInstance.updateSeatState(id, 'available');
                }
            } else {
                // Kiểm tra giới hạn 10 vé
                if (Object.keys(cart).length >= 10) {
                    if (window.showAppToast) {
                        window.showAppToast('error', 'Giới hạn số lượng', 'Bạn chỉ được chọn tối đa 10 vé trong một đơn hàng.');
                    } else {
                        alert('Bạn chỉ được chọn tối đa 10 vé trong một đơn hàng.');
                    }
                    return;
                }

                cart[id] = {
                    id: id,
                    zone: seatEl.dataset.zone,
                    price: Number(seatEl.dataset.price),
                    ticket_id: seatEl.dataset.ticketId
                };
                if (mapInstance) {
                    mapInstance.updateSeatState(id, 'selected');
                }
            }
            updateCartUI();
        };

        document.getElementById('mobile-btn-checkout')?.addEventListener('click', () => {
            document.getElementById('checkout-form').submit();
        });
    </script>
@endsection
