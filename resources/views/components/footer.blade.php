<footer class="mt-12 bg-gray-900 text-white">

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">

        <div class="grid gap-8 md:grid-cols-3">

            {{-- Logo / giới thiệu --}}
            <div>

                <h2 class="text-2xl font-bold text-primary-400">
                    Ticket System
                </h2>

                <p class="mt-3 max-w-sm text-sm leading-6 text-gray-400">
                    Nền tảng đặt vé sự kiện nhanh chóng,
                    thuận tiện và an toàn.
                </p>

            </div>


            {{-- Liên kết --}}
            <div>

                <h3 class="font-semibold">
                    Liên kết
                </h3>

                <div class="mt-4 flex flex-col gap-3">

                    <a href="{{ url('/') }}"
                       class="text-sm text-gray-400 transition hover:text-primary-400">
                        Trang chủ
                    </a>

                    <a href="#"
                       class="text-sm text-gray-400 transition hover:text-primary-400">
                        Sự kiện
                    </a>

                    <a href="#"
                       class="text-sm text-gray-400 transition hover:text-primary-400">
                        Vé của tôi
                    </a>

                </div>

            </div>


            {{-- Hỗ trợ --}}
            <div>

                <h3 class="font-semibold">
                    Hỗ trợ
                </h3>

                <div class="mt-4 flex flex-col gap-3">

                    <a href="#"
                       class="text-sm text-gray-400 transition hover:text-primary-400">
                        Trung tâm hỗ trợ
                    </a>

                    <a href="#"
                       class="text-sm text-gray-400 transition hover:text-primary-400">
                        Điều khoản sử dụng
                    </a>

                    <a href="#"
                       class="text-sm text-gray-400 transition hover:text-primary-400">
                        Chính sách bảo mật
                    </a>

                </div>

            </div>

        </div>


        {{-- Copyright --}}
        <div class="mt-10 border-t border-gray-800 pt-6 text-center">

            <p class="text-sm text-gray-500">
                © {{ date('Y') }} Ticket System.
                All rights reserved.
            </p>

        </div>

    </div>

</footer>