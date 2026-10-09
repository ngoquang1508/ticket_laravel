@extends('layouts.app')

@section('title', 'Chọn vé - ' . $event->name)

@section('content')
    @php
        $layout = $event->seatMap?->layout;
        if (is_string($layout)) {
            $layout = json_decode($layout, true);
        }
        $hasMap = !empty($layout) && !empty($layout['objects']);
        $isAssignedSeat = $event->sale_mode === 'assigned_seat';
    @endphp

    <div class="min-h-screen text-white pt-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-white mb-1">{{ $event->name }}</h1>
                    <p class="text-gray-400 text-sm flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        {{ \Carbon\Carbon::parse($event->starts_at)->format('H:i, d \T\h\á\n\g m, Y') }}
                    </p>
                </div>
                <a href="{{ route('events.show', $event->slug) }}"
                    class="text-sm font-semibold text-gray-300 hover:text-white bg-[#2b2b2b] px-4 py-2 rounded-lg transition self-start md:self-auto">Quay
                    lại sự kiện</a>
            </div>

            <div class="flex flex-col lg:flex-row gap-6">
                <!-- Left: Seat Map or Ticket List -->
                <div class="w-full lg:w-1/2 flex flex-col gap-6">

                    @if($hasMap)
                        <!-- Seat Map Area -->
                        <div class="bg-[#1e1e1e] rounded-2xl border border-gray-800 shadow-xl overflow-hidden flex flex-col">
                            <!-- Map Controls & Legend -->
                            <div
                                class="p-4 border-b border-gray-800 flex flex-wrap items-center justify-between gap-4 bg-[#252525]">
                                <div class="flex items-center gap-4 text-xs font-medium">
                                    @if($isAssignedSeat)
                                        <div class="flex items-center gap-1.5"><span
                                                class="w-3.5 h-3.5 rounded bg-[#3f3f46]"></span> Trống</div>
                                        <div class="flex items-center gap-1.5"><span
                                                class="w-3.5 h-3.5 rounded bg-[#3b82f6]"></span> Đang chọn</div>
                                        <div class="flex items-center gap-1.5"><span
                                                class="w-3.5 h-3.5 rounded bg-[#f97316]"></span> Đang giữ</div>
                                        <div class="flex items-center gap-1.5"><span
                                                class="w-3.5 h-3.5 rounded bg-[#ef4444]"></span> Đã bán</div>
                                    @else
                                        <div class="flex items-center gap-1.5 text-gray-300">
                                            <svg class="w-4 h-4 text-blue-400" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122">
                                                </path>
                                            </svg>
                                            Chọn khu vực bên dưới để xem giá vé
                                        </div>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="button" id="zoom-out"
                                        class="w-8 h-8 rounded bg-[#333] hover:bg-[#444] text-white flex items-center justify-center transition"
                                        title="Thu nhỏ (Zoom Out)">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4">
                                            </path>
                                        </svg>
                                    </button>
                                    <button type="button" id="zoom-in"
                                        class="w-8 h-8 rounded bg-[#333] hover:bg-[#444] text-white flex items-center justify-center transition"
                                        title="Phóng to (Zoom In)">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </button>
                                    <button type="button" id="zoom-reset"
                                        class="px-3 h-8 rounded bg-[#333] hover:bg-[#444] text-white text-xs font-semibold flex items-center justify-center transition"
                                        title="Mặc định">
                                        Reset
                                    </button>
                                </div>
                            </div>

                            <!-- Canvas Container (Pan/Zoom) -->
                            <div id="map-viewport" class="relative w-full overflow-hidden bg-[#121212] rounded-b-2xl h-[400px]"
                                style="touch-action: none;">
                                <div id="seat-map-canvas" class="w-full h-full outline-none"></div>
                            </div>
                        </div>
                    @endif

                    @if(!$isAssignedSeat)
                        <!-- General Admission Ticket Types List -->
                        <div class="bg-[#1e1e1e] rounded-2xl border border-gray-800 shadow-xl p-6">
                            <h3 class="text-xl font-bold mb-4 text-green-500">Các loại vé</h3>
                            <div class="space-y-4" id="ticket-types-container">
                                @foreach($event->ticketTypes as $ticket)
                                    <div class="ticket-type-card bg-[#2b2b2b] rounded-xl p-4 border border-gray-700 flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition-colors duration-300"
                                        data-id="{{ $ticket->id }}" data-price="{{ $ticket->price }}"
                                        data-name="{{ $ticket->name }}">
                                        <div>
                                            <h4 class="font-bold text-lg text-white uppercase">{{ $ticket->name }}</h4>
                                            <div class="text-green-500 font-bold text-lg mt-1">
                                                {{ number_format($ticket->price, 0, ',', '.') }} đ
                                            </div>
                                            <div class="text-sm text-gray-400 mt-1">Còn lại: <span
                                                    class="font-bold text-gray-300">100</span> vé</div>
                                        </div>
                                        <div
                                            class="flex items-center gap-3 bg-[#1e1e1e] p-1.5 rounded-lg border border-gray-700 w-fit">
                                            <button type="button"
                                                class="w-8 h-8 rounded bg-gray-700 hover:bg-gray-600 flex items-center justify-center text-white font-bold transition btn-minus"
                                                data-id="{{ $ticket->id }}">-</button>
                                            <span class="w-8 text-center font-bold text-lg quantity-display"
                                                id="qty-{{ $ticket->id }}">0</span>
                                            <button type="button"
                                                class="w-8 h-8 rounded bg-gray-700 hover:bg-gray-600 flex items-center justify-center text-white font-bold transition btn-plus"
                                                data-id="{{ $ticket->id }}">+</button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>

                <!-- Right: Order Summary (Sticky) -->
                <div class="w-full lg:w-1/2">
                    <div
                        class="bg-[#1e1e1e] rounded-2xl border border-gray-800 shadow-xl lg:sticky lg:top-24 flex flex-col max-h-[calc(100vh-120px)]">
                        <div class="p-6 border-b border-gray-800">
                            <h2 class="text-xl font-bold text-white">Vé của bạn</h2>
                        </div>

                        <div class="p-6 overflow-y-auto flex-1 custom-scrollbar" id="selected-tickets-list">
                            <div class="text-gray-500 text-center italic py-8" id="empty-cart-msg">
                                Chưa có vé nào được chọn.
                            </div>
                            <!-- Items will be injected here via JS -->
                        </div>

                        <div class="p-6 border-t border-gray-800 bg-[#252525] rounded-b-2xl">
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-gray-400">Tổng tạm tính</span>
                                <span class="text-2xl font-bold text-green-500" id="total-price">0 đ</span>
                            </div>
                            <form id="checkout-form" action="#" method="POST">
                                @csrf
                                <input type="hidden" name="cart_data" id="cart-data-input">
                                <button type="submit" id="btn-checkout"
                                    class="w-full py-4 rounded-xl font-bold text-lg transition duration-300 text-[#121212] bg-gray-500 cursor-not-allowed"
                                    disabled>
                                    Tiếp tục thanh toán
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Sticky Bottom Bar -->
    <div
        class="lg:hidden fixed bottom-0 left-0 w-full bg-[#1e1e1e] border-t border-gray-800 shadow-[0_-10px_20px_rgba(0,0,0,0.5)] z-50 p-4 pb-safe flex items-center justify-between gap-4">
        <div class="flex flex-col">
            <span class="text-xs text-gray-400" id="mobile-ticket-count">0 vé</span>
            <span class="text-lg font-bold text-green-500" id="mobile-total-price">0 đ</span>
        </div>
        <button type="button" id="mobile-btn-checkout"
            class="px-6 py-3 rounded-xl font-bold transition duration-300 text-[#121212] bg-gray-500 cursor-not-allowed"
            disabled>
            Tiếp tục
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

                const isAssignedSeat = @json($isAssignedSeat);

                mapInstance = new SeatMapKonva({
                    containerId: 'seat-map-canvas',
                    layout: layout,
                    mode: 'view',
                    isAssignedSeat: isAssignedSeat,
                    onSeatClick: (seatId, obj) => {
                        if (typeof window.toggleSeatSelection === 'function') {
                            // Create mock element for compatibility with toggleSeatSelection
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
                        const card = document.querySelector(`.ticket-type-card[data-id="${obj.ticket_type_id}"]`);
                        if (card) {
                            card.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            card.classList.add('border-blue-500', 'bg-[#223344]');
                            setTimeout(() => card.classList.remove('border-blue-500', 'bg-[#223344]'), 1500);
                        }
                    }
                });

                document.getElementById('zoom-in').addEventListener('click', () => mapInstance.zoomIn());
                document.getElementById('zoom-out').addEventListener('click', () => mapInstance.zoomOut());
                document.getElementById('zoom-reset').addEventListener('click', () => mapInstance.fitToScreen());
            });
        </script>
    @endif

    <script>
        // --- Cart & Checkout Logic ---
        const isAssignedSeat = @json($isAssignedSeat);
        let cart = {}; // For assigned: { seatId: { name, price, ticket_id } }, For GA: { ticket_id: qty }

        function updateCartUI() {
            const list = document.getElementById('selected-tickets-list');
            const emptyMsg = document.getElementById('empty-cart-msg');
            const totalEl = document.getElementById('total-price');
            const mTotalEl = document.getElementById('mobile-total-price');
            const mCountEl = document.getElementById('mobile-ticket-count');
            const btn = document.getElementById('btn-checkout');
            const mBtn = document.getElementById('mobile-btn-checkout');
            const dataInput = document.getElementById('cart-data-input');

            // Clear current items
            list.querySelectorAll('.cart-item').forEach(el => el.remove());

            let total = 0;
            let count = 0;

            if (isAssignedSeat) {
                const seats = Object.values(cart);
                count = seats.length;
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
                                                                                            <button type="button" class="text-red-400 text-xs mt-1 hover:text-red-300 font-semibold" onclick="removeSeat('${seat.id}')">Xóa</button>
                                                                                        </div>
                                                                                    </div>
                                                                                `);
                    });
                } else {
                    emptyMsg.style.display = 'block';
                }
            } else {
                const ticketTypes = @json($event->ticketTypes->keyBy('id'));
                count = Object.values(cart).reduce((a, b) => a + b, 0);
                if (count > 0) {
                    emptyMsg.style.display = 'none';
                    Object.entries(cart).forEach(([id, qty]) => {
                        if (qty > 0) {
                            const ticket = ticketTypes[id];
                            const subtotal = ticket.price * qty;
                            total += subtotal;
                            list.insertAdjacentHTML('beforeend', `
                                                                                        <div class="cart-item bg-[#2b2b2b] p-4 rounded-xl border border-gray-700 mb-3 flex justify-between items-center transition">
                                                                                            <div>
                                                                                                <div class="font-bold text-white">${ticket.name}</div>
                                                                                                <div class="text-sm text-gray-400">${qty} vé</div>
                                                                                            </div>
                                                                                            <div class="text-right">
                                                                                                <div class="font-bold text-green-500">${new Intl.NumberFormat('vi-VN').format(subtotal)} đ</div>
                                                                                            </div>
                                                                                        </div>
                                                                                    `);
                        }
                    });
                } else {
                    emptyMsg.style.display = 'block';
                }
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
                        b.classList.add('bg-[#1db954]', 'text-white', 'hover:bg-white', 'hover:text-black');
                    } else {
                        b.classList.add('bg-gray-500', 'text-[#121212]', 'cursor-not-allowed');
                        b.classList.remove('bg-[#1db954]', 'text-white', 'hover:bg-white', 'hover:text-black');
                    }
                }
            });
        }

        // Exported for inline onclick
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
                seatEl.classList.remove('status-selected');
            } else {
                cart[id] = {
                    id: id,
                    zone: seatEl.dataset.zone,
                    price: Number(seatEl.dataset.price),
                    ticket_id: seatEl.dataset.ticketId
                };
                seatEl.classList.add('status-selected');
            }
            updateCartUI();
        };

        // General Admission buttons
        if (!isAssignedSeat) {
            document.querySelectorAll('.btn-plus').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const id = e.target.dataset.id;
                    cart[id] = (cart[id] || 0) + 1;
                    document.getElementById(`qty-${id}`).textContent = cart[id];
                    updateCartUI();
                });
            });
            document.querySelectorAll('.btn-minus').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const id = e.target.dataset.id;
                    if (cart[id] > 0) {
                        cart[id]--;
                        document.getElementById(`qty-${id}`).textContent = cart[id];
                        updateCartUI();
                    }
                });
            });
        }

        document.getElementById('mobile-btn-checkout')?.addEventListener('click', () => {
            document.getElementById('checkout-form').submit();
        });
    </script>
@endsection