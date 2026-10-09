<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Hướng dẫn thanh toán')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-50 text-slate-800 antialiased">

    <div class="mx-auto w-full max-w-5xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">

        {{-- ================= HEADER ================= --}}
        <header
            class="overflow-hidden rounded-2xl bg-gradient-to-br from-blue-600 via-blue-600 to-indigo-700 text-white shadow-lg">

            <div class="relative px-5 py-6 sm:px-8 sm:py-8">

                {{-- Decorative --}}
                <div class="absolute -right-20 -top-24 h-56 w-56 rounded-full bg-white/10"></div>
                <div class="absolute -bottom-28 -left-16 h-48 w-48 rounded-full bg-white/5"></div>

                <div class="relative">

                    {{-- Status --}}
                    <div class="flex items-center gap-3">

                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white/15 ring-1 ring-white/20">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>

                        <div>
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-blue-100">
                                Đơn hàng của bạn
                            </p>

                            <h1 class="mt-0.5 text-xl font-bold sm:text-2xl">
                                Đang chờ thanh toán
                            </h1>
                        </div>

                    </div>

                    <p class="mt-4 max-w-2xl text-sm leading-relaxed text-blue-100">
                        Vui lòng hoàn tất thanh toán theo hướng dẫn bên dưới.
                        Sau khi thanh toán được xác nhận, vé sẽ được phát hành cho bạn.
                    </p>


                    {{-- Order summary --}}
                    <div class="mt-6 grid gap-3 sm:grid-cols-2">

                        <div class="rounded-xl border border-white/10 bg-white/10 px-4 py-3 backdrop-blur-sm">
                            <p class="text-[10px] font-medium uppercase tracking-wider text-blue-100">
                                Mã đơn hàng
                            </p>

                            <p class="mt-1 font-mono text-sm font-bold tracking-wide">
                                {{ $order->order_code }}
                            </p>
                        </div>

                        <div class="rounded-xl border border-white/10 bg-white/10 px-4 py-3 backdrop-blur-sm">
                            <p class="text-[10px] font-medium uppercase tracking-wider text-blue-100">
                                Tổng thanh toán
                            </p>

                            <p class="mt-1 text-lg font-bold">
                                {{ number_format($order->total_amount, 0, ',', '.') }} ₫
                            </p>
                        </div>

                    </div>

                </div>
            </div>
        </header>


        {{-- ================= MAIN ================= --}}
        <main class="mt-5 sm:mt-6">


            {{-- ================= PAYMENT DEADLINE ================= --}}
            <div class="flex gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4">

                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-amber-600">

                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M12 8v4m0 4h.01M10.29 3.86l-7.82 13.5A2 2 0 004.2 20.4h15.6a2 2 0 001.73-3.04l-7.82-13.5a2 2 0 00-3.42 0z" />
                    </svg>

                </div>

                <div>
                    <p class="text-sm font-semibold text-amber-900">
                        Lưu ý về thời hạn thanh toán
                    </p>

                    <p class="mt-1 text-xs leading-relaxed text-amber-800 sm:text-sm">
                        Đơn hàng sẽ tự động hủy vào
                        <strong>
                            {{ $order->expires_at->format('H:i, d/m/Y') }}
                        </strong>
                        nếu chưa được xác nhận thanh toán.
                        Khi đơn hàng hết hạn, vé hoặc ghế đang giữ sẽ được trả lại hệ thống.
                    </p>
                </div>

            </div>


            {{-- ================= TITLE ================= --}}
            <div class="mt-7">

                <h2 class="text-xl font-bold text-slate-900">
                    Hướng dẫn thanh toán
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Chọn một trong các phương thức dưới đây để hoàn tất đơn hàng.
                </p>

            </div>


            {{-- ================= PAYMENT METHODS ================= --}}
            <div class="mt-5 grid gap-5 lg:grid-cols-2">


                {{-- ================= BANK TRANSFER ================= --}}
                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                    {{-- Card header --}}
                    <div class="border-b border-slate-100 px-5 py-4 sm:px-6">

                        <div class="flex items-center gap-3">

                            <div
                                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">

                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                        d="M3 10h18M5 6h14l2 4H3l2-4zm0 4v8h14v-8M9 14h6" />
                                </svg>

                            </div>

                            <div>
                                <h3 class="font-bold text-slate-900">
                                    Chuyển khoản ngân hàng
                                </h3>

                                <p class="mt-0.5 text-xs text-slate-500">
                                    Thanh toán trực tuyến bằng chuyển khoản
                                </p>
                            </div>

                        </div>

                    </div>


                    {{-- Card body --}}
                    <div class="p-5 sm:p-6">

                        {{-- Step --}}
                        <div class="mb-4 flex items-center gap-2">

                            <span
                                class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-600 text-[11px] font-bold text-white">
                                1
                            </span>

                            <span class="text-sm font-semibold text-slate-900">
                                Thông tin chuyển khoản
                            </span>

                        </div>


                        {{-- Bank information --}}
                        <div class="overflow-hidden rounded-xl border border-slate-200">

                            <div class="flex items-center justify-between gap-4 bg-slate-50 px-4 py-3">
                                <span class="text-xs text-slate-500">
                                    Ngân hàng
                                </span>

                                <span class="text-sm font-semibold text-slate-900">
                                    Vietcombank
                                </span>
                            </div>


                            <div class="flex items-center justify-between gap-4 bg-slate-50 px-4 py-3">
                                <span class="text-xs text-slate-500">
                                    Số tài khoản
                                </span>

                                <span class="text-md font-bold text-blue-900">
                                    0123 456 789
                                </span>

                            </div>


                            <div class="border-t border-slate-200 px-4 py-3">

                                <div class="flex items-center justify-between gap-4">

                                    <span class="text-xs text-slate-500">
                                        Chủ tài khoản
                                    </span>

                                    <span class="text-right text-sm font-semibold text-slate-900">
                                        CÔNG TY TICKET
                                    </span>

                                </div>

                            </div>


                            <div class="border-t border-slate-200 bg-red-50 px-4 py-3">

                                <div class="flex items-center justify-between gap-4">

                                    <span class="text-xs text-slate-600">
                                        Số tiền
                                    </span>

                                    <span class="text-lg font-bold text-red-500">
                                        {{ number_format($order->total_amount, 0, ',', '.') }} ₫
                                    </span>

                                </div>

                            </div>

                        </div>


                        {{-- Transfer content --}}
                        <div class="mt-4 rounded-xl border border-blue-200 bg-blue-50 p-4">

                            <div class="flex gap-3">

                                <div
                                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-blue-600">

                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>

                                </div>

                                <div class="min-w-0">

                                    <p class="text-xs font-semibold text-blue-900">
                                        Nội dung chuyển khoản
                                    </p>

                                    <p class="mt-1 text-[11px] leading-relaxed text-blue-700">
                                        Vui lòng nhập chính xác mã đơn hàng để hệ thống xác định giao dịch.
                                    </p>

                                    <div
                                        class="mt-2 inline-flex max-w-full rounded-lg border border-blue-200 bg-white px-3 py-2">

                                        <span class="break-all font-mono text-sm font-bold text-slate-900">
                                            {{ $order->order_code }}
                                        </span>

                                    </div>

                                </div>

                            </div>

                        </div>


                        {{-- Confirmation --}}
                        <div class="mt-4 flex gap-2 text-xs text-slate-500">

                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-blue-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>

                            <p>
                                Sau khi chuyển khoản, vui lòng chờ hệ thống hoặc quản trị viên xác nhận giao dịch.
                            </p>

                        </div>

                    </div>

                </section>


                {{-- ================= DIRECT PAYMENT ================= --}}
                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                    {{-- Card header --}}
                    <div class="border-b border-slate-100 px-5 py-4 sm:px-6">

                        <div class="flex items-center gap-3">

                            <div
                                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">

                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                        d="M3 21h18M5 21V9l7-5 7 5v12M9 21v-6h6v6M8 10h.01M12 10h.01M16 10h.01" />
                                </svg>

                            </div>

                            <div>
                                <h3 class="font-bold text-slate-900">
                                    Thanh toán tại quầy
                                </h3>

                                <p class="mt-0.5 text-xs text-slate-500">
                                    Thanh toán trực tiếp tại văn phòng
                                </p>
                            </div>

                        </div>

                    </div>


                    {{-- Card body --}}
                    <div class="p-5 sm:p-6">

                        {{-- Step --}}
                        <div class="mb-4 flex items-center gap-2">

                            <span
                                class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-600 text-[11px] font-bold text-white">
                                1
                            </span>

                            <span class="text-sm font-semibold text-slate-900">
                                Đến địa điểm thanh toán
                            </span>

                        </div>


                        {{-- Office --}}
                        <div class="rounded-xl bg-slate-50 p-4">

                            <p class="font-semibold text-slate-900">
                                Văn phòng Công ty Ticket
                            </p>


                            <div class="mt-4 space-y-3">

                                <div class="flex gap-3">

                                    <svg class="mt-0.5 h-4 w-4 shrink-0 text-emerald-600" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">

                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />

                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />

                                    </svg>

                                    <p class="text-xs leading-relaxed text-slate-600">
                                        Tầng 1, Tòa nhà Landmark,<br>
                                        208 Nguyễn Hữu Cảnh, Bình Thạnh,<br>
                                        TP. Hồ Chí Minh
                                    </p>

                                </div>


                                <div class="flex gap-3">

                                    <svg class="mt-0.5 h-4 w-4 shrink-0 text-emerald-600" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">

                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />

                                    </svg>

                                    <p class="text-xs leading-relaxed text-slate-600">
                                        <span class="font-medium text-slate-800">
                                            Giờ làm việc:
                                        </span>
                                        08:30 - 17:30
                                        <br>
                                        Thứ 2 - Thứ 7
                                    </p>

                                </div>

                            </div>

                        </div>


                        {{-- Step 2 --}}
                        <div class="mt-4 flex items-start gap-3">

                            <span
                                class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-600 text-[11px] font-bold text-white">
                                2
                            </span>

                            <div>

                                <p class="text-sm font-semibold text-slate-900">
                                    Cung cấp mã đơn hàng
                                </p>

                                <p class="mt-1 text-xs leading-relaxed text-slate-500">
                                    Đọc mã đơn hàng bên dưới cho nhân viên để xác nhận đơn.
                                </p>

                                <div
                                    class="mt-2 inline-flex rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2">

                                    <span class="font-mono text-sm font-bold text-emerald-800">
                                        {{ $order->order_code }}
                                    </span>

                                </div>

                            </div>

                        </div>


                        {{-- Note --}}
                        <div class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50 p-4">

                            <div class="flex gap-3">

                                <svg class="mt-0.5 h-4 w-4 shrink-0 text-emerald-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">

                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 12c0 5.591 3.824 10.29 9 11.622C17.176 22.29 21 17.591 21 12c0-1.27-.197-2.494-.564-3.016z" />

                                </svg>

                                <p class="text-xs leading-relaxed text-emerald-800">
                                    Sau khi nhân viên xác nhận đã nhận tiền,
                                    đơn hàng sẽ được cập nhật và vé được phát hành cho bạn.
                                </p>

                            </div>

                        </div>

                    </div>

                </section>

            </div>


            {{-- ================= WHAT HAPPENS NEXT ================= --}}
            <section class="mt-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">

                <h2 class="text-sm font-bold text-slate-900 sm:text-base">
                    Sau khi thanh toán thành công
                </h2>

                <div class="mt-4 grid gap-4 sm:grid-cols-3">

                    <div class="flex gap-3">

                        <div
                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-600">
                            <span class="text-xs font-bold">1</span>
                        </div>

                        <div>
                            <p class="text-xs font-semibold text-slate-900">
                                Được xác nhận
                            </p>

                            <p class="mt-1 text-[11px] leading-relaxed text-slate-500">
                                Đơn hàng được cập nhật sang trạng thái đã thanh toán.
                            </p>
                        </div>

                    </div>


                    <div class="flex gap-3">

                        <div
                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-600">
                            <span class="text-xs font-bold">2</span>
                        </div>

                        <div>
                            <p class="text-xs font-semibold text-slate-900">
                                Vé được phát hành
                            </p>

                            <p class="mt-1 text-[11px] leading-relaxed text-slate-500">
                                Hệ thống tạo vé điện tử và QR cho từng vé.
                            </p>
                        </div>

                    </div>


                    <div class="flex gap-3">

                        <div
                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-600">
                            <span class="text-xs font-bold">3</span>
                        </div>

                        <div>
                            <p class="text-xs font-semibold text-slate-900">
                                Nhận vé
                            </p>

                            <p class="mt-1 text-[11px] leading-relaxed text-slate-500">
                                Vé điện tử sẽ xuất hiện trong mục Vé của tôi.
                            </p>
                        </div>

                    </div>

                </div>

            </section>


            {{-- ================= FOOTER ================= --}}
            <div class="mt-6 text-center">

                <a href="{{ route('home') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">

                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>

                    Về trang chủ

                </a>
            </div>

        </main>

    </div>

</body>

</html>