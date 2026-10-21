@extends('layouts.app')

@section('title', 'Thông tin tài khoản - Hồ sơ cá nhân')

@section('content')
    <div class="min-h-screen py-6 sm:py-8">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">

            {{-- ========================================================= --}}
            {{-- MOBILE PROFILE HEADER --}}
            {{-- Chỉ hiển thị trên mobile --}}
            {{-- ========================================================= --}}
            <div class="mb-4 md:hidden">
                @include('user.profile.partials.sidebar', [
                    'mobileOnly' => true
                ])
            </div>


            {{-- ========================================================= --}}
            {{-- MOBILE TABS --}}
            {{-- Chỉ hiển thị trên mobile --}}
            {{-- ========================================================= --}}
            <div class="mb-6 overflow-hidden rounded-2xl border border-white/10 bg-[#292929] p-1.5 shadow-xl md:hidden">
                <nav class="flex gap-1 overflow-x-auto scrollbar-none" aria-label="Profile tabs">
                    <button type="button" data-tab="personal-info" onclick="switchProfileTab('personal-info')"
                        class="profile-nav-btn flex shrink-0 items-center gap-2 rounded-xl border border-transparent px-4 py-2.5 text-xs font-semibold text-gray-400 transition-all hover:bg-white/5 hover:text-white">
                        <x-icons.user class="h-4 w-4 shrink-0" />

                        <span class="whitespace-nowrap">
                            Thông tin cá nhân
                        </span>
                    </button>

                    <button type="button" data-tab="security" onclick="switchProfileTab('security')"
                        class="profile-nav-btn flex shrink-0 items-center gap-2 rounded-xl border border-transparent px-4 py-2.5 text-xs font-semibold text-gray-400 transition-all hover:bg-white/5 hover:text-white">
                        <x-icons.lock class="h-4 w-4 shrink-0" />

                        <span class="whitespace-nowrap">
                            Bảo mật
                        </span>
                    </button>

                    <button type="button" data-tab="tickets" onclick="switchProfileTab('tickets')"
                        class="profile-nav-btn flex shrink-0 items-center gap-2 rounded-xl border border-transparent px-4 py-2.5 text-xs font-semibold text-gray-400 transition-all hover:bg-white/5 hover:text-white">
                        <x-icons.ticket class="h-4 w-4 shrink-0" />

                        <span class="whitespace-nowrap">
                            Vé của tôi
                        </span>
                    </button>
                </nav>
            </div>


            {{-- ========================================================= --}}
            {{-- DESKTOP 2 CỘT / MOBILE 1 CỘT --}}
            {{-- ========================================================= --}}
            <div class="md:flex md:items-start md:gap-8">

                {{-- SIDEBAR DESKTOP --}}
                <aside class="hidden md:block w-72 lg:w-80 shrink-0 sticky top-4 self-start">
                    @include('user.profile.partials.sidebar', [
                        'mobileOnly' => false
                    ])
                </aside>


                {{-- CONTENT --}}
                <main class="w-full flex-1 min-w-0">

                    {{-- Personal info --}}
                    <div id="tab-panel-personal-info" class="profile-tab-panel">
                        @include('user.profile.partials.personal-info')
                    </div>

                    {{-- Security --}}
                    <div id="tab-panel-security" class="profile-tab-panel hidden">
                        @include('user.profile.partials.security')
                    </div>

                    {{-- Tickets --}}
                    <div id="tab-panel-tickets" class="profile-tab-panel hidden">
                        @include('user.profile.partials.tickets')
                    </div>

                </main>

            </div>

        </div>
    </div>


    <script>
        const validTabs = [
            'personal-info',
            'security',
            'tickets'
        ];

        let currentActiveTab =
            "{{ session('active_tab', '') }}"
            || window.location.hash.replace('#', '')
            || 'personal-info';

        if (!validTabs.includes(currentActiveTab)) {
            currentActiveTab = 'personal-info';
        }


        function switchProfileTab(tabName) {
            if (!validTabs.includes(tabName)) {
                return;
            }

            currentActiveTab = tabName;


            /*
            |--------------------------------------------------------------------------
            | Panel
            |--------------------------------------------------------------------------
            */
            validTabs.forEach(tab => {
                const panel = document.getElementById(`tab-panel-${tab}`);

                if (!panel) {
                    return;
                }

                if (tab === tabName) {
                    panel.classList.remove('hidden');
                } else {
                    panel.classList.add('hidden');
                }
            });


            /*
            |--------------------------------------------------------------------------
            | Tab buttons
            |--------------------------------------------------------------------------
            */
            document.querySelectorAll('.profile-nav-btn').forEach(button => {
                const targetTab = button.dataset.tab;

                if (targetTab === tabName) {

                    button.classList.add(
                        'border-emerald-500/30',
                        'bg-emerald-500/10',
                        'text-emerald-400'
                    );

                    button.classList.remove(
                        'border-transparent',
                        'text-gray-400'
                    );

                } else {

                    button.classList.remove(
                        'border-emerald-500/30',
                        'bg-emerald-500/10',
                        'text-emerald-400'
                    );

                    button.classList.add(
                        'border-transparent',
                        'text-gray-400'
                    );
                }
            });


            /*
            |--------------------------------------------------------------------------
            | URL hash
            |--------------------------------------------------------------------------
            */
            try {
                history.replaceState(
                    null,
                    null,
                    `#${tabName}`
                );
            } catch (error) { }
        }


        function togglePasswordVisibility(inputId) {
            const input = document.getElementById(inputId);

            if (!input) {
                return;
            }

            const button = input.parentElement.querySelector('button');

            if (input.type === 'password') {
                input.type = 'text';

                if (button) {
                    button.classList.remove('text-gray-400');
                    button.classList.add('text-emerald-400');
                }
            } else {
                input.type = 'password';

                if (button) {
                    button.classList.remove('text-emerald-400');
                    button.classList.add('text-gray-400');
                }
            }
        }


        document.addEventListener('DOMContentLoaded', function () {

            @if($errors->has('current_password') || $errors->has('password'))
                currentActiveTab = 'security';
            @elseif($errors->has('name') || $errors->has('email') || $errors->has('phone'))
                currentActiveTab = 'personal-info';
            @endif

            switchProfileTab(currentActiveTab);
        });


        window.addEventListener('hashchange', function () {

            const hashTab = window.location.hash.replace('#', '');

            if (validTabs.includes(hashTab)) {
                switchProfileTab(hashTab);
            }
        });
    </script>
@endsection