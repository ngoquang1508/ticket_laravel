<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Thanh toán thành công - {{ config('app.name', 'Ticket') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-50 text-slate-800 antialiased">

    <div class="mx-auto flex min-h-screen w-full max-w-2xl items-center justify-center px-4 py-8 sm:px-6">

        <div class="w-full overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg">


            {{-- ================= SUCCESS HEADER ================= --}}
            <header
                class="relative overflow-hidden bg-gradient-to-br from-emerald-500 via-emerald-500 to-green-600 px-5 py-8 text-white sm:px-8 sm:py-10">

                {{-- Decorative circles --}}
                <div class="absolute -right-20 -top-24 h-56 w-56 rounded-full bg-white/10"></div>
                <div class="absolute -bottom-24 -left-16 h-48 w-48 rounded-full bg-white/5"></div>

                <div class="relative text-center">

                    {{-- Success icon --}}
                    <div
                        class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-white shadow-lg shadow-emerald-900/10">

                        <svg class="h-8 w-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M5 13l4 4L19 7" />
                        </svg>

                    </div>


                    <h1 class="mt-4 text-xl font-bold sm:text-2xl">
                        Thanh toán thành công!
                    </h1>

                    <p class="mx-auto mt-2 max-w-sm text-xs leading-relaxed text-emerald-50 sm:text-sm">
                        Đơn hàng của bạn đã được xác nhận.
                        Vé điện tử đã sẵn sàng cho bạn.
                    </p>


                    {{-- Order code --}}
                    <div
                        class="mx-auto mt-5 max-w-xs rounded-xl border border-white/20 bg-white/10 px-4 py-3 backdrop-blur-sm">

                        <p class="text-[10px] font-medium uppercase tracking-wider text-emerald-100">
                            Mã đơn hàng
                        </p>

                        <p class="mt-1 font-mono text-sm font-bold tracking-wide">
                            {{ $order->order_code }}
                        </p>

                    </div>

                </div>

            </header>


            {{-- ================= CONTENT ================= --}}
            <main class="p-5 sm:p-7">

                {{-- ================= NEXT ACTION ================= --}}
                <div class="mt-5 rounded-xl border border-slate-200 bg-slate-50 p-4">

                    <div class="flex items-start gap-3">

                        <div
                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-900 text-xs font-bold text-white">
                            ✓
                        </div>

                        <div>

                            <p class="text-sm font-semibold text-slate-900">
                                Vé của bạn đã sẵn sàng
                            </p>

                            <p class="mt-1 text-xs leading-relaxed text-slate-500">
                                Bạn có thể xem mã QR và thông tin vé trong mục
                                <strong class="font-semibold text-slate-700">
                                    Vé của tôi
                                </strong>
                                bất cứ lúc nào.
                            </p>

                        </div>

                    </div>

                </div>


                {{-- ================= ACTIONS ================= --}}
                <div class="mt-6 grid gap-3 sm:grid-cols-2">

                    {{-- My tickets --}}
                    <a href="#"
                        class="flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700">

                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M15 5H6a2 2 0 00-2 2v10a2 2 0 002 2h9m0-14l5 4v6l-5 4V5z" />
                        </svg>

                        Xem vé của tôi

                    </a>


                    {{-- Home --}}
                    <a href="{{ route('home') }}"
                        class="flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">

                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M3 10.5L12 3l9 7.5M5 9v11h14V9M9 20v-6h6v6" />
                        </svg>

                        Về trang chủ

                    </a>

                </div>


                {{-- Footer --}}
                <p class="mt-5 text-center text-[10px] leading-relaxed text-slate-400">
                    Vui lòng lưu lại mã đơn hàng
                    <span class="font-mono font-semibold text-slate-500">
                        {{ $order->order_code }}
                    </span>
                    để tra cứu khi cần.
                </p>

            </main>

        </div>

    </div>

</body>

</html>