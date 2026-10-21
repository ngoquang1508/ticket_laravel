@extends('layouts.admin')

@section('title', 'Chi tiết đơn hàng #' . $order->order_code)
@section('page_description', 'Xem chi tiết đơn hàng, thông tin khách hàng và danh sách vé')

@section('content')
    <div class="mx-auto max-w-7xl">
        {{-- Thanh điều hướng & Thao tác đầu trang --}}
        @include('admin.orders.partials.show.header-actions')

        {{-- Cảnh báo nếu đơn đã bị hủy hoặc hết hạn --}}
        @include('admin.orders.partials.show.alert-banner')

        {{-- Bố cục 2 cột chính --}}
        <div class="grid gap-6 lg:grid-cols-3">
            {{-- Cột trái (Chi tiết vé & sản phẩm) --}}
            <div class="space-y-6 lg:col-span-2">
                {{-- Bảng loại vé đã đặt --}}
                @include('admin.orders.partials.show.order-items')

                {{-- Danh sách từng vé phát hành (Tickets) --}}
                @include('admin.orders.partials.show.tickets-list')
            </div>

            {{-- Cột phải (Thông tin phụ trợ: Thanh toán, Khách hàng, Sự kiện) --}}
            <div class="space-y-6">
                {{-- Thông tin thanh toán --}}
                @include('admin.orders.partials.show.payment-info')

                {{-- Thông tin khách hàng --}}
                @include('admin.orders.partials.show.customer-info')

                {{-- Thông tin sự kiện --}}
                @include('admin.orders.partials.show.event-info')
            </div>
        </div>
    </div>

    {{-- Modal xác nhận hủy đơn hàng --}}
    @include('admin.orders.partials.show.cancel-modal')

    {{-- Script xử lý modal và AJAX duyệt đơn hàng --}}
    @include('admin.orders.partials.show.scripts')
@endsection
