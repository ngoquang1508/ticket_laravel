<div class="overflow-hidden rounded-2xl border border-white/10 bg-[#292929] shadow-xl">

    {{-- Header --}}
    <div class="border-b border-white/10 px-5 py-5 sm:px-7">

        <h2 class="text-lg font-bold text-white">
            Bảo mật
        </h2>

        <p class="mt-1 text-sm text-gray-400">
            Thay đổi mật khẩu để bảo vệ tài khoản của bạn.
        </p>

    </div>


    {{-- Form --}}
    <form action="{{ route('user.profile.password') }}" method="POST" class="p-5 sm:p-7">

        @csrf
        @method('PUT')

        <div class="max-w-xl space-y-5">

            {{-- Current password --}}
            <div>

                <label for="current_password" class="block text-sm font-semibold text-gray-200">
                    Mật khẩu hiện tại
                    <span class="text-rose-400">*</span>
                </label>

                <div class="relative mt-2">

                    <input type="password" name="current_password" id="current_password" required
                        autocomplete="current-password" placeholder="••••••••"
                        class="w-full rounded-xl border border-white/10 bg-[#222222] px-4 py-3 pr-11 text-sm text-white placeholder-gray-500 transition focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 @error('current_password') border-rose-500/80 @enderror">

                    <button type="button" onclick="togglePasswordVisibility('current_password')"
                        class="absolute inset-y-0 right-0 flex items-center px-3.5 text-gray-400 transition hover:text-white">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>

                </div>

                @error('current_password')
                    <p class="mt-1.5 text-xs text-rose-400">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- New password --}}
            <div>

                <label for="password" class="block text-sm font-semibold text-gray-200">
                    Mật khẩu mới
                    <span class="text-rose-400">*</span>
                </label>

                <div class="relative mt-2">

                    <input type="password" name="password" id="password" required autocomplete="new-password"
                        placeholder="••••••••"
                        class="w-full rounded-xl border border-white/10 bg-[#222222] px-4 py-3 pr-11 text-sm text-white placeholder-gray-500 transition focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 @error('password') border-rose-500/80 @enderror">

                    <button type="button" onclick="togglePasswordVisibility('password')"
                        class="absolute inset-y-0 right-0 flex items-center px-3.5 text-gray-400 transition hover:text-white">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>

                </div>

                @error('password')
                    <p class="mt-1.5 text-xs text-rose-400">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- Confirm password --}}
            <div>

                <label for="password_confirmation" class="block text-sm font-semibold text-gray-200">
                    Xác nhận mật khẩu mới
                    <span class="text-rose-400">*</span>
                </label>

                <div class="relative mt-2">

                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        autocomplete="new-password" placeholder="••••••••"
                        class="w-full rounded-xl border border-white/10 bg-[#222222] px-4 py-3 pr-11 text-sm text-white placeholder-gray-500 transition focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">

                    <button type="button" onclick="togglePasswordVisibility('password_confirmation')"
                        class="absolute inset-y-0 right-0 flex items-center px-3.5 text-gray-400 transition hover:text-white">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>

                </div>

            </div>

        </div>


        {{-- Footer --}}
        <div class="mt-7 flex justify-end border-t border-white/10 pt-5">

            <button type="submit"
                class="inline-flex items-center gap-2 rounded-xl bg-emerald-500 px-5 py-2.5 text-sm font-bold text-gray-950 transition hover:bg-emerald-400 hover:shadow-lg hover:shadow-emerald-500/20 active:scale-[0.98]">
                Đổi mật khẩu
            </button>

        </div>

    </form>

</div>