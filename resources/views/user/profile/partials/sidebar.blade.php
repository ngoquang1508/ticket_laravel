{{-- ========================================================= --}}
{{-- PROFILE SIDEBAR / MOBILE HEADER --}}
{{-- ========================================================= --}}

@if($mobileOnly ?? false)

    {{-- ===================================================== --}}
    {{-- MOBILE HEADER --}}
    {{-- ===================================================== --}}
    <div class="overflow-hidden rounded-2xl border border-white/10 bg-[#292929] p-4 shadow-xl">

        <div class="flex items-center gap-3.5">

            {{-- Avatar --}}
            <div class="relative shrink-0">

                <div
                    class="flex h-14 w-14 items-center justify-center rounded-full bg-gradient-to-tr from-emerald-600 via-teal-500 to-cyan-400 text-xl font-bold text-white shadow-lg shadow-emerald-500/20 ring-4 ring-white/10">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>

                <div
                    class="absolute bottom-0 right-0 flex h-4 w-4 items-center justify-center rounded-full bg-emerald-500 ring-2 ring-[#292929]">
                    <span class="h-1.5 w-1.5 rounded-full bg-white"></span>
                </div>

            </div>


            {{-- User info --}}
            <div class="min-w-0 flex-1">

                <h1 class="truncate text-base font-bold text-white">
                    {{ $user->name }}
                </h1>

                <p class="mt-0.5 truncate text-xs text-gray-400">
                    {{ $user->email }}
                </p>

            </div>

        </div>

    </div>

@else

    {{-- ===================================================== --}}
    {{-- DESKTOP SIDEBAR --}}
    {{-- ===================================================== --}}
    <div class="relative sticky top-0 space-y-4">

        {{-- User profile --}}
        <div class="overflow-hidden rounded-2xl border border-white/10 bg-[#292929] p-5 shadow-xl">

            <div class="text-center">

                {{-- Avatar --}}
                <div class="relative mx-auto w-fit">

                    <div
                        class="flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-tr from-emerald-600 via-teal-500 to-cyan-400 text-2xl font-bold text-white shadow-lg shadow-emerald-500/20 ring-4 ring-white/10">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>

                    <div
                        class="absolute bottom-0 right-0 flex h-5 w-5 items-center justify-center rounded-full bg-emerald-500 ring-2 ring-[#292929]">
                        <span class="h-2 w-2 rounded-full bg-white"></span>
                    </div>

                </div>


                {{-- Name --}}
                <h1 class="mt-4 truncate text-lg font-bold text-white">
                    {{ $user->name }}
                </h1>

                {{-- Email --}}
                <p class="mt-1 truncate text-sm text-gray-400">
                    {{ $user->email }}
                </p>

            </div>

        </div>


        {{-- Desktop navigation --}}
        <div class="overflow-hidden rounded-2xl border border-white/10 bg-[#292929] p-2 shadow-xl">

            <nav class="flex flex-col gap-1" aria-label="Profile navigation">

                {{-- Personal --}}
                <button type="button" data-tab="personal-info" onclick="switchProfileTab('personal-info')"
                    class="profile-nav-btn group flex w-full items-center gap-3 rounded-xl border border-transparent px-4 py-3 text-left text-sm font-semibold text-gray-400 transition-all hover:bg-white/5 hover:text-white">
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>

                    <span>
                        Thông tin cá nhân
                    </span>

                    <svg class="ml-auto h-4 w-4 opacity-60 transition-transform group-hover:translate-x-0.5" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>


                {{-- Security --}}
                <button type="button" data-tab="security" onclick="switchProfileTab('security')"
                    class="profile-nav-btn group flex w-full items-center gap-3 rounded-xl border border-transparent px-4 py-3 text-left text-sm font-semibold text-gray-400 transition-all hover:bg-white/5 hover:text-white">
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>

                    <span>
                        Bảo mật
                    </span>

                    <svg class="ml-auto h-4 w-4 opacity-60 transition-transform group-hover:translate-x-0.5" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>


                {{-- Tickets --}}
                <button type="button" data-tab="tickets" onclick="switchProfileTab('tickets')"
                    class="profile-nav-btn group flex w-full items-center gap-3 rounded-xl border border-transparent px-4 py-3 text-left text-sm font-semibold text-gray-400 transition-all hover:bg-white/5 hover:text-white">
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                    </svg>

                    <span>
                        Vé của tôi
                    </span>

                    @if(($upcomingTicketsCount ?? 0) > 0)
                        <span class="ml-auto rounded-full bg-emerald-500/20 px-2 py-0.5 text-[10px] font-bold text-emerald-400">
                            {{ $upcomingTicketsCount }}
                        </span>
                    @else
                        <svg class="ml-auto h-4 w-4 opacity-60 transition-transform group-hover:translate-x-0.5" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    @endif
                </button>

            </nav>

        </div>

    </div>

@endif