@extends('layouts.app')

@section('title', '404 - Không tìm thấy trang')

@section('content')
    <div class="min-h-[75vh] flex items-center justify-center px-4 pt-6 sm:px-6 sm:pt-16 lg:px-8">
        <div class="mx-auto w-full max-w-2xl text-center">
            {{-- Ảnh 404 --}}
            <div class="mb-6 flex justify-center">
                <img src="{{ asset('assets/images/404.png') }}" alt="404 - Không tìm thấy trang"
                    class="h-auto w-full max-w-[280px] xs:max-w-[340px] sm:max-w-md md:max-w-lg lg:max-w-xl object-contain select-none drop-shadow-2xl transition duration-300 hover:scale-[1.02]"
                    loading="eager">
            </div>

            {{-- Tiêu đề chính --}}
            <h1 class="text-2xl font-extrabold tracking-tight text-white sm:text-3xl md:text-4xl">
                Không tìm thấy trang yêu cầu
            </h1>
            <p class="mt-2 text-sm text-gray-400 sm:text-base">
                Rất tiếc, trang bạn đang cố gắng truy cập hiện không khả dụng hoặc không tồn tại.
            </p>

            {{-- Các nút điều hướng thao tác --}}
            <div class="mt-8 flex flex-col items-stretch justify-center gap-3 sm:flex-row sm:items-center">
                {{-- Nút quay lại trang trước --}}
                <button type="button" onclick="window.history.back()"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/15 bg-white/10 px-5 py-3 text-sm font-semibold text-white shadow-xs transition hover:bg-white/20 active:scale-95 cursor-pointer">
                    <x-icons.arrow-left class="h-4 w-4" />
                    <span>Quay lại trang trước</span>
                </button>

                {{-- Nút về trang chủ --}}
                <a href="{{ route('home') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-primary-600/30 transition hover:bg-primary-500 active:scale-95">
                    <x-icons.home class="h-4 w-4" />
                    <span>Về trang chủ</span>
                </a>

                {{-- Nút xem danh sách sự kiện --}}
                <a href="{{ route('events.index') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/10 bg-[#1e1e1e] px-5 py-3 text-sm font-semibold text-gray-300 transition hover:bg-white/10 hover:text-white active:scale-95">
                    <x-icons.ticket class="h-4 w-4" />
                    <span>Khám phá sự kiện</span>
                </a>
            </div>
        </div>
    </div>
@endsection