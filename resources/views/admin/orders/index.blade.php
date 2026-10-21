@extends('layouts.admin')

@section('title', 'Quản lý đơn hàng')
@section('page_description', 'Danh sách và trạng thái các đơn hàng trong hệ thống')

@section('content')
    <div class="mx-auto max-w-7xl">
        {{-- Tiêu đề trang --}}
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">Quản lý đơn hàng</h1>
                <p class="mt-1 text-sm text-gray-500">Theo dõi, kiểm tra và xử lý các đơn đặt vé của khách hàng.</p>
            </div>
        </div>

        {{-- Thống kê nhanh số lượng và doanh thu --}}
        @include('admin.orders.partials.index.metrics')

        {{-- Bộ lọc & Tìm kiếm đơn hàng --}}
        @include('admin.orders.partials.index.filters')

        {{-- Bảng danh sách đơn hàng & Phân trang --}}
        @include('admin.orders.partials.index.orders-table')
    </div>

    {{-- Modal xác nhận hủy đơn hàng --}}
    @include('admin.orders.partials.index.cancel-modal')

    {{-- Script xử lý modal và AJAX duyệt đơn hàng --}}
    @include('admin.orders.partials.index.scripts')
@endsection