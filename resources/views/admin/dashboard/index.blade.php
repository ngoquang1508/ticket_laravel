@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_description', 'Tổng quan hoạt động bán vé và doanh thu hệ thống')

@section('content')
    <div class="mx-auto max-w-7xl space-y-6">
        {{-- Tiêu đề trang & Thao tác đầu trang --}}
        @include('admin.dashboard.partials.header')

        {{-- Bộ lọc thống kê (thời gian, sự kiện, khoảng ngày) --}}
        @include('admin.dashboard.partials.filters')

        {{-- 3 Chỉ số KPI cốt lõi (Doanh thu thực tế, Số đơn hàng, Số vé đã bán) --}}
        @include('admin.dashboard.partials.kpi_cards')

        {{-- Phân bố đơn hàng theo trạng thái --}}
        @include('admin.dashboard.partials.order_status')

        {{-- Khu vực biểu đồ doanh thu & Top sự kiện bán chạy nhất --}}
        @include('admin.dashboard.partials.charts')

        {{-- Thống kê tổng quan tài nguyên hệ thống --}}
        @include('admin.dashboard.partials.system_metrics')

        {{-- Sự kiện gần đây / sắp diễn ra & Thao tác nhanh --}}
        <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_340px]">
            @include('admin.dashboard.partials.recent_events')
            @include('admin.dashboard.partials.quick_actions')
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Scripts xử lý biểu đồ và bộ lọc --}}
    @include('admin.dashboard.partials.scripts')
@endpush