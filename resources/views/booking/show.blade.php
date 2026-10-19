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
            {{-- 1. Thông báo trạng thái (Alerts) --}}
            @include('booking.partials.alerts')

            {{-- 2. Banner đơn hàng giữ vé đang chờ thanh toán --}}
            @include('booking.partials.active-order-banner')

            {{-- 3. Tiêu đề sự kiện & Nút quay lại --}}
            @include('booking.partials.event-header')

            <div class="flex flex-col lg:flex-row gap-6">
                {{-- 4. Cột trái: Sơ đồ ghế (SeatMap) hoặc Danh sách loại vé tự do --}}
                <div class="w-full lg:w-1/2 flex flex-col gap-6">
                    @if($hasMap)
                        @include('booking.partials.seat-map')
                    @endif

                    @if(!$isAssignedSeat)
                        @include('booking.partials.ticket-types')
                    @endif
                </div>

                {{-- 5. Cột phải: Tóm tắt đơn vé & Form thanh toán (Desktop) --}}
                <div class="w-full lg:w-1/2">
                    @include('booking.partials.order-summary')
                </div>
            </div>
        </div>
    </div>

    {{-- 6. Thanh thanh toán cố định trên thiết bị di động (Mobile) --}}
    @include('booking.partials.mobile-checkout-bar')

    {{-- 7. Modal thanh toán đơn giữ vé hiện tại --}}
    @if(isset($activeOrder) && $activeOrder && $activeOrder->expires_at > now())
        @include('booking.checkout', ['order' => $activeOrder])
    @endif

    {{-- 8. Scripts điều khiển giỏ vé, sơ đồ ghế và xử lý thanh toán --}}
    @include('booking.partials.scripts')
@endsection