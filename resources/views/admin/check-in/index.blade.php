@extends('layouts.admin')

@section('title', 'Check-in vé')
@section('page_description', 'Quét mã QR hoặc nhập mã vé để kiểm tra và xác nhận check-in cho khách tham gia sự kiện')

@section('content')
    <div class="mx-auto max-w-7xl">
        {{-- Tiêu đề và Thống kê nhanh --}}
        <div class="mb-5 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 flex items-center gap-2.5">
                    <x-icons.qrcode class="h-7 w-7 text-primary-600" />
                    <span>Check-in vé sự kiện</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">Quét camera hoặc nhập mã vé để xác thực và check-in cho khách tham dự.</p>
            </div>

            {{-- Badge thống kê số vé check-in hôm nay --}}
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2.5 rounded-xl border border-emerald-200 bg-emerald-50/70 px-4 py-2.5 text-emerald-800">
                    <span class="flex h-2.5 w-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-xs font-semibold uppercase tracking-wider">Hôm nay đã check-in:</span>
                    <span id="stat-today-count" class="font-mono text-base font-bold text-emerald-900">{{ number_format($todayCount) }}</span>
                    <span class="text-xs text-emerald-700">vé</span>
                </div>
            </div>
        </div>

        {{-- Khối Chọn sự kiện đang check-in (Bắt buộc chọn trước khi soát vé) --}}
        @include('admin.check-in.partials.event-selector')

        {{-- Bố cục 2 cột: Cột trái Camera & Nhập thủ công, Cột phải Kết quả & Lịch sử --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
            {{-- Cột trái (Camera quét QR + Nhập mã thủ công) --}}
            <div class="space-y-6 lg:col-span-6">
                @include('admin.check-in.partials.scanner')
            </div>

            {{-- Cột phải (Kết quả kiểm tra & Lịch sử check-in gần đây) --}}
            <div class="space-y-6 lg:col-span-6">
                @include('admin.check-in.partials.result-card')
                @include('admin.check-in.partials.recent-list')
            </div>
        </div>
    </div>

    {{-- Nhúng logic scripts của trang check-in --}}
    @include('admin.check-in.partials.scripts')
@endsection