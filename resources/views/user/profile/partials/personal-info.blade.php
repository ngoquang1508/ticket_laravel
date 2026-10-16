<div class="overflow-hidden rounded-2xl border border-white/10 bg-[#292929] shadow-xl">

    {{-- Header --}}
    <div class="border-b border-white/10 px-5 py-5 sm:px-7">

        <h2 class="text-lg font-bold text-white">
            Thông tin cá nhân
        </h2>

        <p class="mt-1 text-sm text-gray-400">
            Quản lý và cập nhật thông tin tài khoản của bạn.
        </p>

    </div>


    {{-- Form --}}
    <form action="{{ route('user.profile.update') }}" method="POST" class="p-5 sm:p-7">

        @csrf
        @method('PUT')


        <div class="space-y-5">

            {{-- Name --}}
            <div>

                <label for="name" class="block text-sm font-semibold text-gray-200">
                    Họ và tên
                    <span class="text-rose-400">*</span>
                </label>

                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                    placeholder="Nhập họ và tên"
                    class="mt-2 w-full rounded-xl border border-white/10 bg-[#222222] px-4 py-3 text-sm text-white placeholder-gray-500 transition focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 @error('name') border-rose-500/80 @enderror">

                @error('name')
                    <p class="mt-1.5 text-xs text-rose-400">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- Email (Cố định, không cho phép chỉnh sửa) --}}
            <div>

                <div class="flex items-center justify-between">
                    <label for="email" class="block text-sm font-semibold text-gray-200">
                        Địa chỉ Email
                    </label>
                </div>

                <div class="relative mt-2">
                    <input type="email" id="email" value="{{ $user->email }}" readonly disabled
                        class="w-full rounded-xl border border-white/5 bg-[#1f1f1f] px-4 py-3 text-sm text-gray-400 cursor-not-allowed select-none focus:outline-none focus:ring-0">

                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-500">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                </div>

                <p class="mt-1.5 text-xs text-gray-400">
                    Địa chỉ email được liên kết cố định với tài khoản và mã vé điện tử, không thể chỉnh sửa.
                </p>

            </div>


            {{-- Phone --}}
            <div>

                <label for="phone" class="block text-sm font-semibold text-gray-200">
                    Số điện thoại
                </label>

                <input type="tel" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                    placeholder="0912 345 678"
                    class="mt-2 w-full rounded-xl border border-white/10 bg-[#222222] px-4 py-3 text-sm text-white placeholder-gray-500 transition focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 @error('phone') border-rose-500/80 @enderror">

                @error('phone')
                    <p class="mt-1.5 text-xs text-rose-400">
                        {{ $message }}
                    </p>
                @enderror

            </div>

        </div>


        {{-- Footer --}}
        <div class="mt-7 flex justify-end border-t border-white/10 pt-5">

            <button type="submit"
                class="inline-flex items-center gap-2 rounded-xl bg-emerald-500 px-5 py-2.5 text-sm font-bold text-gray-950 transition hover:bg-emerald-400 hover:shadow-lg hover:shadow-emerald-500/20 active:scale-[0.98]">

                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>

                Lưu thay đổi

            </button>

        </div>

    </form>

</div>