<header class="bg-primary-500 text-white shadow-sm">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="flex h-16 items-center gap-6">

            {{-- Logo --}}
            <a href="{{ url('/') }}" class="shrink-0 text-2xl font-bold">
                Ticket System
            </a>


            {{-- Search desktop --}}
            <div class="hidden flex-1 justify-center md:flex">
                <div class="w-full max-w-md">
                    @include('components.search-bar', [
                        'id' => 'desktop-search'
                    ])
                </div>
            </div>


            {{-- Desktop navigation --}}
            <nav class="hidden shrink-0 items-center gap-6 md:flex">

                {{-- Trang chủ --}}
                <a href="{{ url('/') }}" class="whitespace-nowrap text-white/90 transition hover:text-white">
                    Trang chủ
                </a>


                {{-- Sự kiện --}}
                <a href="#" class="whitespace-nowrap text-white/90 transition hover:text-white">
                    Sự kiện
                </a>


                {{-- Vé của tôi --}}
                <a href="#" class="whitespace-nowrap text-white/90 transition hover:text-white">
                    Vé của tôi
                </a>


                {{-- Guest --}}
                @guest

                    <div class="flex items-center text-sm font-semibold">

                        <a href="{{ route('login') }}" class="whitespace-nowrap transition hover:text-white/80">
                            Đăng nhập
                        </a>

                        <span class="mx-1 text-white/60">
                            |
                        </span>

                        <a href="{{ route('register') }}" class="whitespace-nowrap transition hover:text-white/80">
                            Đăng ký
                        </a>

                    </div>

                @else

                    {{-- User menu --}}
                    <div class="relative">

                        {{-- User button --}}
                        <button id="user-menu-button" type="button" class="flex items-center gap-2 rounded-xl px-2 py-2
                                       transition hover:bg-white/10" aria-expanded="false">

                            {{-- Avatar --}}
                            <div class="flex h-8 w-8 items-center justify-center
                                           rounded-full
                                           bg-white/20
                                           font-semibold
                                           backdrop-blur-md">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>


                            {{-- Name --}}
                            <span class="max-w-32 truncate font-medium">
                                {{ Auth::user()->name }}
                            </span>


                            {{-- Arrow --}}
                            <svg id="user-menu-arrow" class="h-4 w-4 transition-transform duration-200" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>

                        </button>


                        {{-- Liquid Glass Dropdown --}}
                        <div id="user-menu" class="pointer-events-none absolute right-0 z-50 mt-2 w-48
                                       origin-top-right
                                       overflow-hidden
                                       rounded-2xl
                                       border border-white/20
                                       bg-primary-900/75
                                       py-2
                                       text-white
                                       shadow-[0_8px_32px_rgba(0,0,0,0.25)]
                                       backdrop-blur-2xl
                                       opacity-0
                                       scale-95
                                       translate-y-1
                                       transition-all
                                       duration-200
                                       ease-out">

                            {{-- Tài khoản --}}
                            <a href="#" class="block px-4 py-2.5
                                           text-sm
                                           text-white/85
                                           transition
                                           hover:bg-white/10
                                           hover:text-white">
                                Tài khoản
                            </a>


                            {{-- Vé của tôi --}}
                            <a href="#" class="block px-4 py-2.5
                                           text-sm
                                           text-white/85
                                           transition
                                           hover:bg-white/10
                                           hover:text-white">
                                Vé của tôi
                            </a>


                            {{-- Divider --}}
                            <div class="my-1 border-t border-white/15"></div>


                            {{-- Logout --}}
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf

                                <button type="submit" class="block w-full
                                               px-4 py-2.5
                                               text-left
                                               text-sm
                                               font-medium
                                               text-red-300
                                               transition
                                               hover:bg-red-400/10
                                               hover:text-red-500">
                                    Đăng xuất
                                </button>
                            </form>

                        </div>

                    </div>

                @endguest

            </nav>


            {{-- Mobile menu button --}}
            <button id="mobile-menu-button" type="button" class="ml-auto rounded-lg p-2
                       transition hover:bg-white/10
                       md:hidden" aria-label="Mở menu" aria-expanded="false">

                {{-- Hamburger --}}
                <svg id="menu-icon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>


                {{-- Close --}}
                <svg id="close-icon" xmlns="http://www.w3.org/2000/svg" class="hidden h-6 w-6" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>

            </button>

        </div>


        {{-- Search mobile --}}
        <div class="border-t border-white/20 py-3 md:hidden">

            @include('components.search-bar', [
                'id' => 'mobile-search'
            ])

        </div>


        {{-- Mobile navigation --}}
        <nav id="mobile-menu" class="max-h-0 overflow-hidden
                   opacity-0
                   transition-all
                   duration-300
                   ease-in-out
                   md:hidden">

            <div class="flex flex-col gap-2
                       border-t border-white/20
                       py-4">

                {{-- Trang chủ --}}
                <a href="{{ url('/') }}" class="rounded-lg px-4 py-3
                           transition hover:bg-white/10">
                    Trang chủ
                </a>


                {{-- Sự kiện --}}
                <a href="#" class="rounded-lg px-4 py-3
                           transition hover:bg-white/10">
                    Sự kiện
                </a>


                {{-- Vé của tôi --}}
                <a href="#" class="rounded-lg px-4 py-3
                           transition hover:bg-white/10">
                    Vé của tôi
                </a>


                @guest

                    {{-- Đăng nhập --}}
                    <a href="{{ route('login') }}" class="mt-2 rounded-lg
                                   bg-white
                                   px-4 py-3
                                   text-center
                                   font-medium
                                   text-primary-600
                                   transition
                                   hover:bg-primary-50">
                        Đăng nhập
                    </a>


                    {{-- Đăng ký --}}
                    <a href="{{ route('register') }}" class="rounded-lg
                                   border border-white
                                   px-4 py-3
                                   text-center
                                   font-medium
                                   transition
                                   hover:bg-white/10">
                        Đăng ký
                    </a>

                @else

                    {{-- Mobile user --}}
                    <div class="mt-2
                                   border-t border-white/20
                                   pt-4">

                        <p class="px-4 text-sm text-white/70">
                            Xin chào
                        </p>

                        <p class="px-4 pt-1 font-medium text-white">
                            {{ Auth::user()->name }}
                        </p>


                        {{-- Logout --}}
                        <form action="{{ route('logout') }}" method="POST" class="mt-3 px-4">
                            @csrf

                            <button type="submit" class="w-full
                                           rounded-lg
                                           border border-white
                                           px-4 py-3
                                           text-center
                                           font-medium
                                           text-white
                                           transition
                                           hover:bg-red-400/20">
                                Đăng xuất
                            </button>

                        </form>

                    </div>

                @endguest

            </div>

        </nav>

    </div>
</header>


{{-- Scripts --}}
<script>

    /*
    |--------------------------------------------------------------------------
    | Mobile menu
    |--------------------------------------------------------------------------
    */

    const menuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    const menuIcon = document.getElementById('menu-icon');
    const closeIcon = document.getElementById('close-icon');

    if (menuButton && mobileMenu && menuIcon && closeIcon) {

        menuButton.addEventListener('click', () => {

            const isOpen =
                mobileMenu.classList.contains('max-h-[500px]');


            if (isOpen) {

                // Close menu
                mobileMenu.classList.remove(
                    'max-h-[500px]',
                    'opacity-100'
                );

                mobileMenu.classList.add(
                    'max-h-0',
                    'opacity-0'
                );

                menuIcon.classList.remove('hidden');
                closeIcon.classList.add('hidden');

                menuButton.setAttribute(
                    'aria-expanded',
                    'false'
                );

            } else {

                // Open menu
                mobileMenu.classList.remove(
                    'max-h-0',
                    'opacity-0'
                );

                mobileMenu.classList.add(
                    'max-h-[500px]',
                    'opacity-100'
                );

                menuIcon.classList.add('hidden');
                closeIcon.classList.remove('hidden');

                menuButton.setAttribute(
                    'aria-expanded',
                    'true'
                );

            }

        });

    }


    /*
    |--------------------------------------------------------------------------
    | User dropdown
    |--------------------------------------------------------------------------
    */

    const userMenuButton =
        document.getElementById('user-menu-button');

    const userMenu =
        document.getElementById('user-menu');

    const userMenuArrow =
        document.getElementById('user-menu-arrow');


    if (userMenuButton && userMenu) {

        userMenuButton.addEventListener('click', () => {

            const isOpen =
                userMenuButton.getAttribute('aria-expanded') === 'true';


            if (isOpen) {

                closeUserMenu();

            } else {

                openUserMenu();

            }

        });


        /*
        |--------------------------------------------------------------------------
        | Click outside
        |--------------------------------------------------------------------------
        */

        document.addEventListener('click', (event) => {

            if (
                !userMenuButton.contains(event.target) &&
                !userMenu.contains(event.target)
            ) {
                closeUserMenu();
            }

        });


        /*
        |--------------------------------------------------------------------------
        | Open
        |--------------------------------------------------------------------------
        */

        function openUserMenu() {

            userMenu.classList.remove(
                'opacity-0',
                'scale-95',
                'translate-y-1',
                'pointer-events-none'
            );

            userMenu.classList.add(
                'opacity-100',
                'scale-100',
                'translate-y-0'
            );


            if (userMenuArrow) {
                userMenuArrow.classList.add('rotate-180');
            }


            userMenuButton.setAttribute(
                'aria-expanded',
                'true'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Close
        |--------------------------------------------------------------------------
        */

        function closeUserMenu() {

            userMenu.classList.remove(
                'opacity-100',
                'scale-100',
                'translate-y-0'
            );

            userMenu.classList.add(
                'opacity-0',
                'scale-95',
                'translate-y-1',
                'pointer-events-none'
            );


            if (userMenuArrow) {
                userMenuArrow.classList.remove('rotate-180');
            }


            userMenuButton.setAttribute(
                'aria-expanded',
                'false'
            );

        }

    }

</script>